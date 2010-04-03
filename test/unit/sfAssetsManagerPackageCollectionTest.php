<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/model/sfAssetsManagerPackageCollection.class.php';

$config = include dirname(__FILE__).'/../fixtures/assets_manager.yml.php';

$t = new lime_test(2, new lime_output_color);

$t->comment('Accessors');

$collection = new sfAssetsManagerPackageCollection();
$collection->fromArray($config['packages']);

$t->isa_ok($collection->get('basic'), 'sfAssetsManagerPackage', '->get() returns a sfAssetsManagerPackage');
$t->is($collection->get('unexistant'), null, '->get() return null if no package matches the name');
