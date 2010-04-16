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
  protected  $config;
  
  /**
   * @property sfResponse
   */
  protected $response;
             
  /**
   * @property sfConfigCache
   */
  protected $configCache;
   
  /**
   * @property sfAssetsManagerPackageCollection
   */
  protected $packages;
  
  /**
   * @var sfAssetsManager
   */
  static protected $instance;

  
  /**
   * @param boolean $autoload Should the configuration be automatically loaded
   * @param sfResponse $response
   * @param sfConfigCache $configCache
   */
  public function __construct($autoload = true, $configCache = null, $response = null)
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
  public function load($packageName, $assetsType = null)
  {
    if(is_array($packageName))
    {
      foreach($packageName as $name)
      {
        $this->load($name, $assetsType);
      }
    }
    
    $package = $this->packages->get($packageName);
    if(!$package)
    {
      throw new sfConfigurationException(sprintf('No package called "%s" could be found.', $packageName));
    }
    
    $javascripts = $assetsType !== 'css'
                 ? $package->getJavascripts()
                 : array();
    $stylesheets = $assetsType !== 'js'
                 ? $package->getStylesheets()
                 : array();
    
    if(sfConfig::get('app_sf_assets_manager_plugin_enable_compressor', false))
    {
      $javascripts = (array) $this->compress($javascripts, 'js', $packageName);
      $stylesheets = (array) $this->compress($stylesheets, 'css', $packageName);
    }
    
    $this->addToResponse($javascripts, $stylesheets);
  }
  
  
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
    
    
    $filename = sfConfig::get('app_sf_assets_manager_plugin_encode_filename', false)
              ? md5($name)
              : $name.'.package';

    $outputUri = sprintf('/%s/%s.%s',sfConfig::get(sprintf('sf_web_%s_dir_name', $type), $type), $filename, $type);
    $outputPath = sfConfig::get('sf_web_dir').$outputUri;
    
    // create file
    rename($minified, $outputPath);
    
    return $outputUri;
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
   * Loads the assets_manager.yml files
   */
  protected function loadPackagesConfiguration()
  {
    $this->setConfiguration(include($this->getConfigCache()->checkConfig('config/assets_manager.yml')));
  }
  

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
  public function setConfiguration($config)
  {
    if(!isset($config['packages']))
    {
      throw new sfConfigurationException(sprintf('The %s class requires configuration with "packages" root key.', __CLASS__));
    }
    $this->config = $config;
    $this->setPackages($config['packages']);
  }
  
  
  /**
   * Retrieves the global configuration or load the configuration file
   * @return array The configuration array
   */
  public function getConfiguration()
  {
    return $this->config;
  }
  
  
  /**
   * @param sfAssetsManagerPackageCollection $packages
   */
  public function setPackages($packages)
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
  
}