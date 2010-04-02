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
             $config;
  static protected $instance;
  
                    
  public function __construct()
  {}
                    
  /**
   * Loads assets from configuration
   * @param string|array $packages package or array of packages to load
   * @param string $assetsType 'js' or 'css'. Defines wich type of assets to load (all by default)
   * @return unknown_type
   */
  public function load($packages, $assetsType = null)
  {
    $config = $this->getConfiguration();
    if(!$config || !isset($config['packages']))
    {
      throw new sfConfigurationException(sprintf('The %s->load() method requires configuration array() with [packages]. No such configuration exists.', __CLASS__));
    }
    if(is_array($packages))
    {
      foreach((array) $packages as $package)
      {
        $this->loadSinglePackage($package, $config, $assetsType);
      }
    }
    else
    {
      $this->loadSinglePackage($packages, $config, $assetsType);
    }
  }
  
  /**
   * Loads a package datas
   * @param string $package
   * @param array $config
   * @param string $assetsType [optional, default = null]. 'js' or 'css'
   */
  protected function loadSinglePackage($package, $config, $assetsType = null)
  {
    $this->log(sprintf('Loading package "%s", asset of type "%s"', $package, $assetsType!==null ? $assetsType : 'all'));
    if(!isset($config['packages'][$package]))
    {
      throw new sfConfigurationException(sprintf('No asset package "%s" found.', $package));
    }
    $assets = $config['packages'][$package];
    if(isset($assets['import']))
    {
      $this->load($assets['import'], $assetsType);
    }
    if(isset($assets['js']) && ($assetsType === null || $assetsType === 'js'))
    {
      is_array($assets['js'])
          ? $this->addJavascripts($assets['js'])
          : $this->addJavascript($assets['js']);
    }
    if(isset($assets['css']) && ($assetsType === null || $assetsType === 'css'))
    {
      is_array($assets['css'])
          ? $this->addStylesheets($assets['css'])
          : $this->addStylesheet($assets['css']);
    }
  }
  
  
  /**
   * Creates an object and uses it as a Singleton.
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
   * Adds a javascript path to the response
   * @param string $js
   */
  public function addJavascript($js)
  {
    $this->log(sprintf('Adding javascript "%s" to the Response', $js));
    return $this->getResponse()->addJavascript($js);
  }
  
  /**
   * Adds an array of javascripts path to the reponse
   * @param array $jss
   */
  public function addJavascripts($jss)
  {
    foreach($jss as $js)
    {
      $this->addJavascript($js);
    }
  }
  
  
  
  
  /**
   * Adds a stylesheet to the Response
   * @param string $css path to the css file
   */
  public function addStylesheet($css)
  {
    $this->log(sprintf('Adding stylesheet "%s" to the Response', $css));
    return $this->getResponse()->addStylesheet($css);
  }
  
  /**
   * Adds an array of css path
   * @param array $csss
   */
  public function addStylesheets($csss)
  {
    foreach($csss as $css)
    {
      $this->addStylesheet($css);
    }
  }
  
  
  /**
   * Injects a Response object into this class
   */
  public function setResponse(sfWebResponse $response)
  {
    $this->response = $response;
  }
  
  
  /**
   * Retrieves the global configuration or load the configuration file
   * @return array The configuration array
   */
  public function getConfiguration()
  {
    if(!$this->config)
    {
      $this->config = include(sfContext::getInstance()->getConfigCache()->checkConfig('config/assets_manager.yml'));
    }
    return $this->config;
  }
  
  
  /**
   * Inject a configuration array
   * @param array $config
   */
  public function setConfiguration($config)
  {
    $this->config = $config;
  }
  
  
  public function loadConfigurationFile($file)
  {
    $this->setConfiguration(sfYaml::load($file));
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