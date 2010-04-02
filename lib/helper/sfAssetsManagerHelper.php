<?php

/**
 * Loads assets into the Response from configuration file
 * @param string $package
 * @param string $assetsType optional
 */
function load_assets($package, $assetsType = null)
{
  sfAssetsManager::getInstance()->load($package, $assetsType);
}