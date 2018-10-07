<?php

echo 'Create Models Process - Start'. PHP_EOL;

$moduleDir = dirname(__DIR__);
$vendorDir = dirname(dirname($moduleDir));

require_once $vendorDir . '/autoload.php';

ModelGenerator\Common\Helper\Config::setModuleDir($moduleDir);
ModelGenerator\Common\Helper\Config::setVendorDir($vendorDir);

$configDir = ModelGenerator\Common\Helper\Config::getConfigDir();

if(!file_exists($configDir . '/database.local.php')){
    throw new Exception('You must create config/database.local.php');
}

$config = include $configDir . '/database.local.php' ;

$infoSchema = new \ModelGenerator\Common\Schema\Model\Information( $config['db'] );
$infoSchema->load(['table_schema' => $config['db']['database']]);

$generator = new \ModelGenerator\Common\Schema\Model\Generator($infoSchema);
$generator->init()->create();

echo 'Create Models Process - End'. PHP_EOL;