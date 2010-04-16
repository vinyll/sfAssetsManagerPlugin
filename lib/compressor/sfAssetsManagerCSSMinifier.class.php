<?php
require_once dirname(__FILE__).'/MinifyCSSCompressor.class.php';
require_once dirname(__FILE__).'/sfAssetsManagerMinifier.class.php';
/**
 * Minify a javascript file
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage  compressor
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfAssetsManagerCSSMinifier extends sfAssetsManagerMinifier
{
  /**
   * Minifies a stylesheet file
   * @param string $file  Absolute path of file to minify
   * @return path of the tmp minified file
   */
  public function execute($file)
  {
    $content = MinifyCSSCompressor::process($this->readFile($file));
    return $this->writeContent($content);
  }
  
}