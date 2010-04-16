<?php

class sfAssetsManagerDebugPanel extends sfWebDebugPanel
{
  
  protected static $loadedPackages = array();
  
  
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/config.png" alt="sfAssetsManagerPlugin informations" /> Assets';
  }

  public function getPanelTitle()
  {
    return 'Assets manager';
  }

  public function getPanelContent()
  {
    try
    {
      $manager = sfAssetsManager::getInstance();
      $packages = $manager->getPackages();
      $loadedPackages = self::$loadedPackages;
      ob_start();
      include(dirname(__FILE__).'/_panel.html.php');
      $html = ob_get_contents();
      ob_end_clean();
    }
    catch (Exception $e)
    {
      $html = 'Unable to render the sfAssetsManager toolbar';
    }

    return $html;
  }

  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('assets_manager', new sfAssetsManagerDebugPanel($event->getSubject()));
  }
  
  
  static public function listenToLoadPackageEvent(sfEvent $event)
  {
    self::$loadedPackages = $event->getSubject()->getLoadedPackages();
  }

}
