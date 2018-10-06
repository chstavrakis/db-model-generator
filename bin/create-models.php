<?php

echo 'Create Models Process - Start'. PHP_EOL;


$dirName =  dirname(__DIR__);

require $dirName . '/vendor/autoload.php';

if(!file_exists($dirName . '/config/database.local.php')){
    throw new Exception('You must create config/database.local.php');
}

$config = include $dirName . '/config/database.local.php' ;
$dbParams = $config['db'];
$adapter = new \Zend\Db\Adapter\Adapter($dbParams);

$modelDir = $dirName . '/src/Model';

var_dump($modelDir);

echo 'Create Models Process - End'. PHP_EOL;