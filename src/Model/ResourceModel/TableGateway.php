<?php

namespace ModelGenerator\Model\ResourceModel;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class TableGateway extends AbstractTableGateway
{

    protected $primaryKey;

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->_construct();
    }

    protected function _construct()
    {

    }

    public function _init($tableName, $primaryKey)
    {
        $this->table = $tableName;
        $this->primaryKey = $primaryKey;
        $this->initialize();
    }

    public function load($where = array(), $columns = array(), $order = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'main_table' => $this->getTable()
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

    public function loadById($id)
    {
        //load by primaryKey
        return $this->load([$this->getPrimaryKey() => $id]);
    }
}