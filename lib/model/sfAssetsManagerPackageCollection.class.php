<?php
require_once dirname(__FILE__).'/sfAssetsManagerPackage.class.php';

/**
 * @author Vincent Agnano <vincent.agnano@particul.es>
 * @package sfAssetsManager
 * @subpackage model
 */
class sfAssetsManagerPackageCollection implements Iterator
{
  protected $packages = array();
  
  /**
   * Set values from a formatted array
   * @param array $packages
   * @return sfAssetsManagerCollection
   */
  public function fromArray($packages)
  {
    foreach((array) $packages as $name => $config)
    {
      $package = new sfAssetsManagerPackage($name);
      $package->fromArray($config);
      $this->add($package);
    }
    return $this;
  }
  
  
  /**
   * Add a package to the collection
   * @return sfAssetsManagerCollection
   */
  public function add(sfAssetsManagerPackage $package)
  {
    $package->setCollection($this);
    $this->packages[$package->getName()] = $package;
    return $this;
  }
  
  
  /**
   * Get a package by its name
   * @param string $name
   * @return sfAssetsManagerCollection
   */
  public function get($name)
  {
    if(isset($this->packages[$name]))
    {
      return $this->packages[$name];
    }
  }
  
  /**
   * Removes a package by its name
   * @param string $name
   * @return sfAssetsManagerCollection
   */
  public function remove($name)
  {
    if(isset($this->packages[$name]))
    {
      unset($this->packages[$name]);
    }
    return $this;
  }
  
  
  /**
   * Get collection as an array
   * @return array
   */
  public function toArray()
  {
    $list = array();
    foreach($this->packages as $name => $package)
    {
      $list[$name] = $package;
    }
    return $list;
  }
  
  
  public function rewind()
  {
    reset($this->packages);
  }
  
  public function next()
  {
    return next($this->packages);
  }
  
  public function key()
  {
    return key($this->packages);
  }
  
  public function valid()
  {
    return isset($this->packages[key($this->packages)]);
  }
  
  public function current()
  {
    return current($this->packages);
  }
  
}