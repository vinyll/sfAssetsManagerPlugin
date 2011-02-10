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

/**
 * Retrieves the stylesheets tags from the response or the tmp css tag 
 * @return string
 */
function stylesheet_assets()
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Asset');
  // Update response after it gets totally loaded 
  if(sfConfig::get('app_sf_assets_manager_plugin_alter_response', false))
  {
    return sfConfig::get('app_sf_assets_manager_plugin_alter_response_tempcsstag');
  }
  // Use regular helper on render time
  else
  {
    return get_stylesheets();
  }
}

/**
 * Retrieves the javascripts tags from the response or the tmp js tag 
 * @return string
 */
function javascript_assets()
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('Asset');
  // Update response after it gets totally loaded 
  if(sfConfig::get('app_sf_assets_manager_plugin_alter_response', false))
  {
    return sfConfig::get('app_sf_assets_manager_plugin_alter_response_tempjstag');
  }
  // Use regular helper on render time
  else
  {
    return get_javascripts();
  }
}