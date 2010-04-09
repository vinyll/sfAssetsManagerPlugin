<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/model/sfAssetsManagerPackageCollection.class.php';

$config = include dirname(__FILE__).'/../fixtures/assets_manager.yml.php';

$response = new sfWebResponse(new sfEventDispatcher);
$manager = new sfAssetsManager(false, null, $response);
$manager->setConfiguration($config);


$t = new lime_test(13, new lime_output_color);

$manager->load('basic');
$t->diag('Basic js/css package');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js'), '->load(basic) includes multiple js in specified order');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css'), '->load(basic) includes multiple css in specified order');

$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
$manager->load('complex');
$t->diag('Importing package');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js'), '->load(complex) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(complex) imports specified package and includes specific js');

$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
$manager->load('nested');
$t->diag('Importing nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js', 'nested.js'), '->load(nested) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(nested) imports specified package and includes specific css');

$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
$manager->load('full');
$t->diag('Importing multiple and nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array('framework.js', 'basic.js', '/folder/otherbasic.js', 'complex1.js', 'complex2.js', 'nested.js', 'full.js'), '->load(full) imports specified package and includes specific js');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css', 'full.css'), '->load(full) imports specified package and includes specific css');

$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
$manager->load('basic', 'css');
$t->diag('Including css only');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array(), '->load(basic, css) excludes js from package');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css'), '->load(basic, css) includes css from package');

$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
$manager->load('nested', 'css');
$t->diag('Importing css only from nested packages');
$t->is_deeply(array_keys($manager->getResponse()->getJavascripts()), array(), '->load(nested, css) excludes all css from all packages');
$t->is_deeply(array_keys($manager->getResponse()->getStylesheets()), array('basic.css', '/folder/otherbasic.css', 'complex1.css', 'complex2.css'), '->load(nested, css) imports css from nested packages');


$manager->setResponse(new sfWebResponse(new sfEventDispatcher));
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
