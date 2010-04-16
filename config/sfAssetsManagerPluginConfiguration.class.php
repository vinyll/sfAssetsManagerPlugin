<?php
class sfAssetsManagerPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('debug.web.load_panels', array('sfAssetsManagerDebugPanel', 'listenToLoadPanelEvent'));
    $this->dispatcher->connect('sfAssetsManagerPlugin.load_package', array('sfAssetsManagerDebugPanel', 'listenToLoadPackageEvent'));
  }
}