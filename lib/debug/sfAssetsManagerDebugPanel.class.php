<?php
/**
 * Debug toolbar "Assets manager" section
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage  compressor
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfAssetsManagerDebugPanel extends sfWebDebugPanel
{
  /**
   * List of packages loaded to the response
   * @var array
   */
  protected static $loadedPackages = array();
  
  
  /**
   * @see lib/vendor/symfony/lib/debug/sfWebDebugPanel#getTitle()
   */
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/config.png" alt="sfAssetsManagerPlugin informations" /> Assets';
  }

  
  /**
   * @see lib/vendor/symfony/lib/debug/sfWebDebugPanel#getPanelTitle()
   */
  public function getPanelTitle()
  {
    return 'Assets manager';
  }

  /**
   * @see lib/vendor/symfony/lib/debug/sfWebDebugPanel#getPanelContent()
   */
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

  
  /**
   * Listening for initialization
   * @param sfEvent $event
   */
  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('assets_manager', new sfAssetsManagerDebugPanel($event->getSubject()));
  }
  
  
  /**
   * Listening when packages are being loaded
   * @param sfEvent $event
   */
  static public function listenToLoadPackageEvent(sfEvent $event)
  {
    self::$loadedPackages = $event->getSubject()->getLoadedPackages();
  }

}
