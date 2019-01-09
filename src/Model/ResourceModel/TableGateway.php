<?php

namespace ModelGenerator\Model\ResourceModel;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class TableGateway extends AbstractTableGateway
{

    public $table;

    public function __construct($table, Adapter $adapter)
    {
        $this->table = $table;
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->initialize();
    }


    public function load($where = array(), $columns = array(), $order = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'main_table' => $this->table
            ));

            if (count($where) > 0) {
                $select->where($where);
            }

            if (count($columns) > 0) {
                $select->columns($columns);
            }

            if (count($order) > 0) {
                $select->order($order);
            }

            $statement = $sql->prepareStatementForSqlObject($select);

            return $this->resultSetPrototype->initialize($statement->execute());

        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
}