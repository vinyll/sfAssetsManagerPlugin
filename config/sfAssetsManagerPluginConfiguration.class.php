<?php
class sfAssetsManagerPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('debug.web.load_panels', array('sfAssetsManagerDebugPanel', 'listenToLoadPanelEvent'));
    $this->dispatcher->connect('sfAssetsManagerPlugin.load_package', array('sfAssetsManagerDebugPanel', 'listenToLoadPackageEvent'));
    
    if(sfConfig::get('app_sf_assets_manager_plugin_autoload_helper', false))
    {
      $helpers = array_merge(sfConfig::get('sf_standard_helpers', array()), array('sfAssetsManager'));
      sfConfig::set('sf_standard_helpers', $helpers);
    }
    
    if(sfConfig::get('app_sf_assets_manager_plugin_alter_response', false))
    {
      $this->dispatcher->connect('response.filter_content', array('sfAssetsManager', 'alterResponse'));
    }
  }
}