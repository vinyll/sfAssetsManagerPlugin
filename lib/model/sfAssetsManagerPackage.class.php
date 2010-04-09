<?php
/**
 * @author Vincent Agnano <vincent.agnano@particul.es>
 * @package sfAssetsManager
 * @subpackage model
 *
 * @method setStylesheets
 * @method addStylesheets
 * @method getStylesheets array
 * @method setJavascripts
 * @method addJavascripts
 * @method getJavascripts array
 * @method setImports
 * @method addImports
 * @method getImports array
 */
class sfAssetsManagerPackage
{
  protected $name,
            $import = array(),
            $js = array(),
            $css = array(),
            /**
             * @property sfAssetsManagerPackageCollection
             */
            $collection
            ;
  
  public function __construct($name)
  {
    $this->name = $name;
  }
    
  
  /**
   * Package name
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  
  
  /**
   * Accessors definition
   * @param string $method
   * @param array $arguments
   * @return mixed
   */
  public function __call($method, $arguments = array())
  {
    $action = substr($method, 0, 3);
    $property = isset($arguments[0]) ? $arguments[0] : null;
    $values = isset($arguments[1]) ? (array) $arguments[1] : null;
    if(!in_array($action, array('get', 'set', 'add')) || !isset($this->{$property}))
    {
      throw new sfRuntimeException('Method "%s" does not exists for class %s', $method, getclass($this));
    }
    switch($action)
    {
      case 'get':
        return $this->{$property};
        break;
      case 'set':
        $this->{$property} = $values;
        return $this;
        break;
      case 'add':
        foreach($values as $value)
        {
          if(!in_array($value, $this->{$property}))
          {
            $this->{$property}[] = $value;
          }
        }
        return $this;
        break;
    }
  }
  
  
  /**
   * Set values from a formatted array
   * @param array $config
   * @return sfAssetsManagerCollection
   */
  public function fromArray($config)
  {
    foreach((array) $config as $key => $value)
    {
      if(isset($this->$key))
      {
        $this->$key = (array) $value;
      }
    }
    return $this;
  }
  
  
  /**
   * Retrieves all the stylesheets from this package and imported packages
   * @return array
   */
  public function getStylesheets()
  {
    return $this->getAssets('css', 'getStylesheets');
  }
  
  
  /**
   * Retrieves all the javascript from this package and imported packages
   * @return array
   */
  public function getJavascripts()
  {
    return $this->getAssets('js', 'getJavascripts');
  }
  
  
  /**
   * Common method for getJavascripts() and getStylesheets().
   * Retrieves the requested type of assets merged with imported packaged
   * @param string $key property name to retrieve ('css' or 'js')
   * @param strind $method method name to recursively invoke on imported Packages
   * ('getStylesheets' or 'getJavascripts')
   * @return array
   */
  protected function getAssets($key, $method)
  {
    $assets = array();
    foreach($this->get('import') as $import)
    {
      $package = $this->collection->get($import);
      if(!$package)
      {
        throw new sfConfigurationException(sprintf('Package "%s" failed to import non existant "%s" package.', $this->name, $import));
      }
      $assets = array_merge($assets, $package->$method());
    }
    return array_merge($assets, $this->get($key));
  }
  
  
  /**
   * Get values formatted in an array
   * @return array
   */
  public function toArray()
  {
    return array(
      'import'  => $this->import,
      'js'      => $this->js,
      'css'     => $this->css,
    );
  }
  
  
  /**
   *
   * @param sfAssetsManagerPackageCollection $collection
   * @return sfAssetsManagerPackage
   */
  public function setCollection(sfAssetsManagerPackageCollection $collection)
  {
    $this->collection = $collection;
    return $this;
  }
  
  
  /**
   *
   * @return sfAssetsManagerPackageCollection
   */
  public function getCollection()
  {
    return $this->collection;
  }
  
  
}