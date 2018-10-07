<?php

echo 'Create Models Process - Start'. PHP_EOL;

$dirName = dirname(dirname(dirname(dirname(__DIR__))));

$moduleDir = dirname(__DIR__);
$vendorDir = dirname(dirname($moduleDir));

require_once $vendorDir . '/autoload.php';

if(!file_exists($moduleDir . '/config/database.local.php')){
    throw new Exception('You must create config/database.local.php');
}

$config = include $moduleDir . '/config/database.local.php' ;

$infoSchema = new \ModelGenerator\Common\Schema\Model\Information( $config['db'] );
$infoSchema->load(['table_schema' => $config['db']['database']]);

$generator = new \ModelGenerator\Common\Schema\Model\Generator($infoSchema);
$generator->init()->create();

echo 'Create Models Process - End'. PHP_EOL;