<?php

namespace ModelGenerator\Model\ResourceModel;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * Class TableGateway
 *
 * @package ModelGenerator\Model\ResourceModel
 */
class TableGateway extends AbstractTableGateway
{

    /**
     * @var
     */
    protected $primaryKey;

    /**
     * TableGateway constructor.
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->_construct();
    }

    /**
     * Protected constructor
     */
    protected function _construct()
    {
    }

    /**
     * @param $tableName
     * @param $primaryKey
     */
    public function _init($tableName, $primaryKey)
    {
        $this->table = $tableName;
        $this->primaryKey = $primaryKey;
        $this->initialize();
    }

    /**
     * @param array $where
     * @param array $columns
     * @param array $order
     *
     * @return mixed
     * @throws \Exception
     */
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

    /**
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function loadById($id)
    {
        return $this->load([$this->getPrimaryKey() => $id]);
    }

    /**
     * @return mixed
     */
    protected function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}