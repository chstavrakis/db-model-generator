<?php

echo 'Create Models Process - Start'. PHP_EOL;

$moduleDir = dirname(__DIR__);
$vendorDir = dirname(dirname($moduleDir));

require_once $vendorDir . '/autoload.php';

ModelGenerator\Common\Helper\Config::setModuleDir($moduleDir);
ModelGenerator\Common\Helper\Config::setVendorDir($vendorDir);


$generator = new \ModelGenerator\Core\Generator();
$config = $generator->getConfig()['db'];

$infoSchema = new \ModelGenerator\Core\Model\InformationSchema( $config );
$infoSchema->load(['table_schema' => $config['database']]);

$generator = new \ModelGenerator\Common\Schema\Model\ModelGenerator($infoSchema);
$generator->init()->create();

echo 'Create Models Process - End'. PHP_EOL;
