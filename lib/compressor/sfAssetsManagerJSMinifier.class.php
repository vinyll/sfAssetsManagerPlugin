<?php
require_once dirname(__FILE__).'/sfAssetsManagerMinifier.class.php';
require_once dirname(__FILE__).'/../vendor/jsmin/jsmin.php';
/**
 * Minify a stylesheet file
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage  compressor
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfAssetsManagerJSMinifier extends sfAssetsManagerMinifier
{
  /**
   * Minifies a javascript file
   * @param string $file  Absolute path of file to minify
   * @return path of the tmp minified file
   */
  public function execute($file)
  {
    $content = JSMin::minify($this->readFile($file));
    return $this->writeContent($content);
  }
  
}