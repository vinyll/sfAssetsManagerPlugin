<?php
class sfAssetsManagerCache
{
  protected $useSupercache = true,
            $isEnabled = false;
  
  
  public function __construct()
  {
    $this->isEnabled = sfConfig::get('app_sf_assets_manager_plugin_enable_supercache', false);
  }
            
  public function hasFile($filename)
  {
    
  }
  
  
  public function isEnabled()
  {
    return $this->isEnabled;
  }
}