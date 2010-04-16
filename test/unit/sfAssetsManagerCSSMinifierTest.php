<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/compressor/sfAssetsManagerCSSMinifier.class.php';

$fileDir = dirname(__FILE__).'/../fixtures/css';
$minifier = new sfAssetsManagerCSSMinifier;
$file = $minifier->execute($fileDir.'/expanded.css');

$t = new lime_test(2, new lime_output_color);
$t->ok(file_exists($file), '->execute() returns a file path');
$t->is(file_get_contents($file), "html{margin:0;padding:0}#content{color:#000}", '->execute() compresses the js file content');