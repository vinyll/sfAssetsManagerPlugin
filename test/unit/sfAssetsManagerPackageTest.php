<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/model/sfAssetsManagerPackage.class.php';


$t = new lime_test(7, new lime_output_color);

$t->comment('Accessors');

$package = new sfAssetsManagerPackage('test');

$t->is($package->get('css'), array(), '->get() method returns an empty array by default');

$package->set('css', 'script.css');
$t->is($package->get('css'), array('script.css'), '->set() and ->get() write and read property');

$package->add('import', 'reference1');
$package->add('import', 'reference2');
$t->is($package->get('import'), array('reference1', 'reference2'), '->add() adds reference packages retrieved by ->get()');


$t->comment('Configuration from array');
$package = new sfAssetsManagerPackage('test2');
$package->fromArray(array(
  'import'  => 'reference',
  'js'      => 'script.js',
  'css'     => array('style1.css', 'style2.css'),
));
$t->is($package->get('import'), array('reference'), '->fromArray() inject imports property');
$t->is($package->get('js'), array('script.js'), '->fromArray() inject javascripts property');
$t->is($package->get('css'), array('style1.css', 'style2.css'), '->fromArray() inject stylesheets property');

$t->is_deeply($package->toArray(),
              array('import' => array('reference'), 'js' => array('script.js'), 'css' => array('style1.css', 'style2.css')),
              '->toArray() exports values into an array');
