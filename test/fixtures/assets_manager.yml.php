<?php
return array(
  'packages' => array(
    'basic'   => array(
      'js'    => array(
        'basic.js', '/folder/otherbasic.js'
      ),
      'css' => array(
        'basic.css', '/folder/otherbasic.css'
      )
    ),
    'complex' => array(
      'import'  => 'basic',
      'js'      => array(
        'complex1.js', 'complex2.js',
      ),
      'css'     => array(
        'complex1.css', 'complex2.css',
      )
    ),
    'nested'  => array(
      'import'  => 'complex',
      'js'      => 'nested.js',
    ),
    'framework'  => array(
      'js'        => 'framework.js'
    ),
    'full'      => array(
      'import'  =>  array('framework', 'nested'),
      'js'  => 'full.js'
      ,
      'css' => 'full.css',
    ),
    'existing'  => array(
      'js'      => array('file1.js', 'file2.js')
    )
  )
);