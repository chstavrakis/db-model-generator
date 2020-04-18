<?php

namespace ModelGenerator\Core\TableGateway;

use Zend\Db\TableGateway\TableGatewayInterface;


interface GeneratorTableGatewayInterface extends TableGatewayInterface
{
    public function load($where, $columns, $order, $limit);
    /**
     * Update statement
     *
     * @param array $id
     * @param array $setData
     *
     * @return int
     * @throws \Exception
     */
    public function updateById($id, $setData = []);
    public function save();
}