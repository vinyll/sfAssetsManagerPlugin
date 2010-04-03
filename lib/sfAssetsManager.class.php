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
  protected  $response,
             $config,
             /**
              * @property sfAssetsManagerPackageCollection
              */
             $packages;
             
  static protected $instance;

  
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
    
    $this->addToResponse($javascripts, $stylesheets);
  }
  
  
  /**
   * Add assets to the Response
   * @param array $javascripts
   * @param array $stylesheets
   * @return sfWebResponse
   */
  protected function addtoResponse($javascripts, $stylesheets)
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
   * Retrieves the global configuration or load the configuration file
   * @return array The configuration array
   */
  public function getConfiguration()
  {
    if(!$this->config)
    {
      $this->setConfiguration(include(sfContext::getInstance()->getConfigCache()->checkConfig('config/assets_manager.yml')));
    }
    return $this->config;
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
  
  
  protected function log($message)
  {
    //print "\n".$message."\n";
  }
  
}