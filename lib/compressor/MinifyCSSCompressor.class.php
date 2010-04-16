<?php
require_once dirname(__FILE__).'/../vendor/minify/lib/Compressor.php';
/**
 * Fix for Minify_CSS_Compressor class
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage  compressor
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class MinifyCSSCompressor extends Minify_CSS_Compressor
{
  private function __construct($options) {
    $this->_options = $options;
  }
    
  public static function process($css, $options = array())
  {
    $obj = new self($options);
    return $obj->_process($css);
  }
  
  
  protected function _process($css)
  {
    $css = parent::_process($css);
    $css = preg_replace('`\s*`', '', $css);
    $css = preg_replace('`;}`', '}', $css);
    return $css;
  }
}