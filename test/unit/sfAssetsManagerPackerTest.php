<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/compressor/sfAssetsManagerPacker.class.php';

$fileDir = dirname(__FILE__).'/../fixtures/js';

$t = new lime_test(2, new lime_output_color);

$packer = new sfAssetsManagerPacker;
$file = $packer->execute(array(
  $fileDir.'/file1.js',
  $fileDir.'/file2.js'
));

$t->ok(file_exists($file), '->execute() creates a temporary file');
$t->is(file_get_contents($file), "var file1 = 'file1 content';\nvar file2 = 'file2 content';\n", '->execute() packs multiple files content into 1 file');
