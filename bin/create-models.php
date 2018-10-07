<?php

echo 'Create Models Process - Start'. PHP_EOL;

$dirName = dirname(dirname(dirname(dirname(__DIR__))));

require_once $dirName . '/vendor/autoload.php';

if(!file_exists($dirName . '/config/database.local.php')){
    throw new Exception('You must create config/database.local.php');
}

$config = include $dirName . '/config/database.local.php' ;

$infoSchema = new \ModelGenerator\Common\Schema\Model\Information( $config['db'] );
$infoSchema->load(['table_schema' => $config['db']['database']]);

$generator = new \ModelGenerator\Common\Schema\Model\Generator($infoSchema);
$generator->init()->create();

echo 'Create Models Process - End'. PHP_EOL;