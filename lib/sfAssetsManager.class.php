<?php
/**
 * Manage Response assets
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfAssetsManager
{
  /**
   * @property sfContext
   */
  protected $context;
  
  /**
   * @property sfResponse
   */
  protected $response;
             
  /**
   * @property sfConfigCache
   */
  protected $configCache;
             
  /**
   * @property sfEventDispatcher
   */
  protected $dispatcher;
   
  /**
   * @property sfAssetsManagerPackageCollection
   */
  protected $packages;
  
  /**
   * List of packages that have been loaded
   * @property array
   */
  protected $loadedPackages = array();
  
  /**
   * @var sfAssetsManager
   */
  static protected $instance;

  
  /**
   * @param boolean $autoload Should the configuration be automatically loaded
   * @param sfResponse $response
   * @param sfConfigCache $configCache
   */
  public function __construct(sfWebResponse $response = null, $autoload = true, sfConfigCache $configCache = null)
  {
    if($response)
    {
      $this->setResponse($response);
    }
    if($configCache)
    {
      $this->setConfigCache($configCache);
    }
    if($autoload)
    {
      $this->loadPackagesConfiguration();
    }
  }
  
  
  /**
   * Creates an object and uses it as a Singleton.
   * This can should though be used as a regular object. Only use this if specifically required.
   * @return sfAssetManager
   */
  static public function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  
  /**
   * Loads assets from configuration
   * @param string|array $packageName package(s) name to load
   * @param string $assetsType 'js', 'css' or null for both. Defines wich type of assets to load
   *                            (null by default)
   */
  public function load($name, $type = null)
  {
    // supercache
    if($this->loadFromCache($name, $type))
    {
      $package = $this->packages->get($name);
      $this->packageLoaded($package, $type, 'supercache');
      return;
    }
    
    if(is_array($name))
    {
      foreach($name as $singleName)
      {
        $this->load($singleName, $type);
      }
    }
    
    if($this->packages === null)
    {
      throw new LogicException('Unable to load a package if no configuration is setup.');
    }
    $package = $this->packages->get($name);
    if(!$package)
    {
      if(sfConfig::get('app_sf_assets_manager_plugin_display_errors', true) === true)
      {
        throw new sfConfigurationException(sprintf('No package called "%s" could be found.', $name));
      }
      return;
    }
    
    $javascripts = $type !== 'css'
                 ? $package->getJavascripts()
                 : array();
    $stylesheets = $type !== 'js'
                 ? $package->getStylesheets()
                 : array();
    
    if(sfConfig::get('app_sf_assets_manager_plugin_enable_compressor', false))
    {
      $javascripts = (array) $this->compress($javascripts, 'js', $name);
      $stylesheets = (array) $this->compress($stylesheets, 'css', $name);
    }
    
    $this->addToResponse($javascripts, $stylesheets);
    $this->packageLoaded($package, $type, 'realtime');
  }
  
  
  /**
   * Checks if a supercache file exists and load it
   * @param string $name Package name
   * @param string $type "js" or "css"
   * @return boolean true if loaded from cache
   */
  protected function loadFromCache($name, $type = null)
  {
    if(sfConfig::get('app_sf_assets_manager_plugin_enable_supercache', false)
      && sfConfig::get('app_sf_assets_manager_plugin_enable_compressor', false))
    {
      $javascript = $type !== 'css' && file_exists(sfConfig::get('sf_web_dir').$this->getFileUri($name, 'js'))
                   ? $this->getFileUri($name, 'js')
                   : null;
      $stylesheet = $type !== 'js' && file_exists(sfConfig::get('sf_web_dir').$this->getFileUri($name, 'css'))
                   ? $this->getFileUri($name, 'css')
                   : null;
      if($javascript || $stylesheet)
      {
        $this->addToResponse((array) $javascript, (array) $stylesheet);
        return true;
      }
    }
    return false;
  }
  
  
  /**
   * Stores and notifies that a package was loaded
   * @param sfAssetsManagerPackage $package
   * @param string $type
   * @param string $source Source of the loading
   */
  protected function packageLoaded(sfAssetsManagerPackage $package, $type, $source = 'Realtime')
  {
    $this->loadedPackages[] = array(
      'package' => $package,
      'type'    => $type,
      'source'  => $source
    );
    
    if($this->getDispatcher())
    {
      $this->getDispatcher()->notify(new sfEvent($this, 'sfAssetsManagerPlugin.load_package'), array(
        'package'  => $package,
        'type'     => $type,
        'source'   => $source
      ));
    }
  }
  
  
  /**
   * Pack files, minify contents and save result into a file.
   * @param array $files
   * @param sting $type "js" or "css"
   * @param string $name
   * @return string The web path of the created compressed file
   */
  protected function compress(array $files, $type, $name)
  {
    if(empty($files))
    {
      return;
    }
    
    // Abslotute files path
    $sources = array();
    foreach($files as $file)
    {
      $sources[] = $this->getAbsoluteDir($file, $type);
    }
    
    // merge files
    $packer = new sfAssetsManagerPacker;
    $packed = $packer->execute($sources);
    
    // minify file
    $minifier = new sfAssetsManagerJSMinifier;
    $minified = $minifier->execute($packed);
    
    $outputUri = $this->getFileUri($name, $type);
    $outputPath = sfConfig::get('sf_web_dir').$outputUri;
    
    // create file
    rename($minified, $outputPath);
    
    return $outputUri;
  }
  
  
  /**
   * Get the URI for a name and a type
   * @param string $name package name
   * @param string $type "css" or "js"
   * @return string
   */
  protected function getFileUri($name, $type)
  {
    $filename = sfConfig::get('app_sf_assets_manager_plugin_encode_filename', false)
              ? md5($name)
              : sprintf(sfConfig::get('app_sf_assets_manager_plugin_filename_format', '%s'), $name);
    return sprintf('/%s/%s.%s',sfConfig::get(sprintf('sf_web_%s_dir_name', $type), $type), $filename, $type);
  }
  
  
  /**
   * Add assets to the Response
   * @param array $javascripts
   * @param array $stylesheets
   * @return sfWebResponse
   */
  protected function addtoResponse(array $javascripts, array $stylesheets)
  {
    $response = $this->getResponse();
    foreach($javascripts as $js)
    {
      $response->addJavascript($js);
    }
    foreach($stylesheets as $css)
    {
      $response->addStylesheet($css);
    }
    return $response;
  }
  
  
  /**
   * Alters the response upon an event replacing the temp css tag and temp js tag
   * with the actual stylesheets and javascripts items read from the response.
   * @param sfEvent $event
   * @param string $response
   * @return string the altered response content
   */
  static public function alterResponse(sfEvent $event, $response)
  {
    $self = self::getInstance();
    $self->getContext()->getConfiguration()->loadHelpers(array('Tag', 'Asset'));
    $subject = $event->getSubject();
    // Stylesheets
    $css = '';
    foreach($subject->getStylesheets() as $file => $options)
    {
      $css .= stylesheet_tag($file, $options);
    }
    $tmpCssTag = sfConfig::get('app_sf_assets_manager_plugin_alter_response_tempcsstag');
    // Javascripts
    $js = '';
    foreach($subject->getJavascripts() as $file => $options)
    {
      $js .= javascript_include_tag($file, $options);
    }
    $tmpJsTag = sfConfig::get('app_sf_assets_manager_plugin_alter_response_tempjstag');
    
    $altered = preg_replace(sprintf('`%s`', $tmpCssTag), $css, $response);
    return preg_replace(sprintf('`%s`', $tmpJsTag), $js, $altered);
  }
  
  
  /**
   * Loads the assets_manager.yml files
   */
  protected function loadPackagesConfiguration()
  {
    $this->setConfiguration(include($this->getConfigCache()->checkConfig('config/assets_manager.yml')));
  }
  
  
  /**
   * Computes a absolute asset ressource.
   * @param string $file Relative or absolute file or url
   * @param string $type "js" or "css"
   * @return string Return the absolute ressource (path or url)
   */
  protected function getAbsoluteDir($file, $type)
  {
    // Compute a absolute local web path
    if(substr($file, 0, 1) === '/')
    {
      $absolute = sfConfig::get('sf_web_dir').$file;
    }
    // Compute a url
    elseif(preg_match('`^https?://.*`', $file))
    {
      $absolute = $file;
    }
    // Compute relative dir
    else
    {
      $dir = sfConfig::get(
        sprintf('app_sf_assets_manager_plugin_%s_dir', $type),
        sfConfig::get('sf_web_dir').'/'.sfConfig::get(sprintf('sf_web_%s_dir_name', $type), $type)
      );
      $absolute = $dir.'/'.$file;
    }
    return $absolute;
  }
  
  
  /**
   * Inject a configuration array
   * @param array $config
   */
  public function setConfiguration(array $config)
  {
    if(!isset($config['packages']))
    {
      throw new sfConfigurationException(sprintf('The %s class requires configuration with "packages" root key.', __CLASS__));
    }
    $this->setPackages($config['packages']);
  }
  
  
  /**
   * @param sfAssetsManagerPackageCollection $packages
   */
  public function setPackages(array $packages)
  {
    $this->packages = new sfAssetsManagerPackageCollection();
    $this->packages->fromArray($packages);
  }

  
  /**
   * @return sfAssetsManagerPackageCollection
   */
  public function getPackages()
  {
    return $this->packages;
  }
  
  
  /**
   * Injects a Response object into this class
   * @param sfWebResponse $response
   */
  public function setResponse(sfWebResponse $response)
  {
    $this->response = $response;
  }
  
  
  /**
   * Return the injected response, or the context response object
   * by default.
   * @return sfWebResponse
   */
  public function getResponse()
  {
    if(!$this->response)
    {
      $this->response = sfContext::getInstance()->getResponse();
    }
    return $this->response;
  }
  
  
  /**
   * @param sfConfigCache $configCache
   */
  public function setConfigCache(sfConfigCache $configCache)
  {
    $this->configCache = $configCache;
  }
  
  
  /**
   *
   * @return sfConfigCache
   */
  public function getConfigCache()
  {
    if(!$this->configCache)
    {
      return sfContext::getInstance()->getConfigCache();
    }
    return $this->configCache;
  }
  
  
  /**
   * @param sfEventDispatcher $dispatcher
   */
  public function setDispatcher(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }
  
  
  /**
   * @return sfEventDispatcher
   */
  public function getDispatcher()
  {
    if(!$this->dispatcher && sfContext::hasInstance())
    {
      $this->dispatcher = $this->getContext()->getEventDispatcher();
    }
    return $this->dispatcher;
  }
  
  
  /**
   * Get the list of packages that habe been loaded
   * @return array
   */
  public function getLoadedPackages()
  {
    return $this->loadedPackages;
  }
  
  
  public function getContext()
  {
    if(!$this->context)
    {
      $this->context = sfContext::getInstance();
    }
    return $this->context;
  }
  
}