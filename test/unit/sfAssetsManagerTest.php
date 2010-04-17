<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/model/sfAssetsManagerPackageCollection.class.php';
require dirname(__FILE__).'/../../lib/compressor/sfAssetsManagerJSMinifier.class.php';

$config = include dirname(__FILE__).'/../fixtures/assets_manager.yml.php';

sfConfig::set('app_sf_assets_manager_plugin_filename_format', '%s.package');

$response = new sfWebResponse(new sfEventDispatcher);
$manager = new sfAssetsManager(clone $response, false);
$manager->setConfiguration($config);

$t = new lime_test(15, new lime_output_color);

$manager->load('basic');
$t->diag('Basic js/css package');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js'), '->load(basic) includes multiple js in specified order');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css'), '->load(basic) includes multiple css in specified order');

$manager->setResponse(clone $response);
$manager->load('complex');
$t->diag('Importing package');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js'), '->load(complex) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(complex) imports specified package and includes specific js');

$manager->setResponse(clone $response);
$manager->load('nested');
$t->diag('Importing nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js', 'nested.js'), '->load(nested) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(nested) imports specified package and includes specific css');

$manager->setResponse(clone $response);
$manager->load('full');
$t->diag('Importing multiple and nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('framework.js', 'basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js', 'nested.js', 'full.js'), '->load(full) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css', 'full.css'), '->load(full) imports specified package and includes specific css');

$manager->setResponse(clone $response);
$manager->load('basic', 'css');
$t->diag('Including css only');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array(), '->load(basic, css) excludes js from package');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css'), '->load(basic, css) includes css from package');

$manager->setResponse(clone $response);
$manager->load('nested', 'css');
$t->diag('Importing css only from nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array(), '->load(nested, css) excludes all css from all packages');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(nested, css) imports css from nested packages');


$manager->setResponse(clone $response);
$message = "->load(unexistant-package) Throw a sfConfigurationException if package is not found.";
try
{
  $manager->load('unexistant-package');
  $t->fail($message);
}
catch(sfConfigurationException $e)
{
  $t->pass($message);
}


// Compressing
$t->diag('Compressing a package');

// config
$webDir = dirname(__FILE__).'/../web';
sfConfig::set('sf_web_dir', $webDir);
sfConfig::set('app_sf_assets_manager_plugin_enable_compressor', true);
sfConfig::set('app_sf_assets_manager_plugin_js_dir', dirname(__FILE__).'/../fixtures/js');

$manager->load('existing', 'js');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('/js/existing.package.js'), '->load() loads a package and sends the file path to the response');
unlink($webDir.'/js/existing.package.js');

sfConfig::set('app_sf_assets_manager_plugin_encode_filename', true);
$manager->setResponse(clone $response);
$manager->load('existing', 'js');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('/js/'.md5('existing').'.js'), '->load() loads a package and sends the the encoded file path to the response');
unlink($webDir.'/js/'.md5('existing').'.js');
