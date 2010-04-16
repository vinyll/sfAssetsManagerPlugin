<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/compressor/sfAssetsManagerJSMinifier.class.php';

$fileDir = dirname(__FILE__).'/../fixtures/js';
$minifier = new sfAssetsManagerJSMinifier;
$file = $minifier->execute($fileDir.'/expanded.js');

$t = new lime_test(2, new lime_output_color);
$t->ok(file_exists($file), '->execute() returns a file path');
$t->is(file_get_contents($file), "\nvar uncompressed='';var uncompressed2=2", '->execute() compresses the js file content');