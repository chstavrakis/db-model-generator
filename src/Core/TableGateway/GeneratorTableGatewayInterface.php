<?php

namespace ModelGenerator\Core\TableGateway;

use Zend\Db\TableGateway\TableGatewayInterface;


interface GeneratorTableGatewayInterface extends TableGatewayInterface
{
    public function load($where, $columns, $order);
    public function save();
}