<?php
abstract class sfAssetsManagerMinifier
{
  
  /**
   * Minifies a file
   * @param string $file  Absolute path of file to minify
   * @param boolean $write Write to a file. false would return raw content
   * @return path of the minified file
   */
  abstract public function execute($file);
  
  
  /**
   * Reads a file
   * @param string $file Absolute path of the file to read
   * @return string Content of the file
   */
  protected function readFile($file)
  {
    $content = @file_get_contents($file);
    if($content === false)
    {
      throw new RuntimeException(sprintf('Unable to read from "%s"', $file));
    }
    return $content;
  }
  
  
  /**
   * Writes a string into a temp file
   * @param string $content
   * @return string Absolute path of the temp file created
   */
  protected function writeContent($content)
  {
    $target = tempnam(sys_get_temp_dir(), 'amc');
    if(file_put_contents($target, $content) === false)
    {
      throw new RuntimeException(sprintf('Unable to write to file "%s".', $target));
    }
    return $target;
  }
}