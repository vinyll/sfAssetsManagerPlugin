<?php
/**
 * Pack multiple files
 *
 * @package     sfAssetsManagerPlugin
 * @subpackage  compressor
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfAssetsManagerPacker
{
  
  /**
   * Packs multiple files into one single file
   * @param array $files Absolute path to files to pack
   * @return path of the tmp packed file
   */
  public function execute(array $files)
  {
    $content = '';
    foreach($files as $file)
    {
      $source = @file_get_contents($file);
      if($source === false)
      {
        throw new RuntimeException(sprintf('Unable to read from "%s"', $file));
      }
      $content .= sprintf("%s\n", $source);
    }
    
    $target = tempnam(sys_get_temp_dir(), 'amc');
    if(file_put_contents($target, $content) === false)
    {
      throw new RuntimeException(sprintf('Unable to write to file "%s".', $target));
    }
    
    return $target;
  }
  
}