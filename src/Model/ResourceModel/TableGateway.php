<?php

namespace ModelGenerator\Model\ResourceModel;


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
     * @var string
     */
    protected $primaryKey;

    /**
     * TableGateway constructor.
     *
     */
    public function __construct()
    {
        /**
         * Please override this one instead of overriding real __construct constructor
         */
        $this->_construct();
    }

    /**
     * Protected constructor
     */
    protected function _construct(){}

    /**
     * @param Adapter $adapter
     */
    protected function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
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
            $sql = $this->getSql();
            $select = $sql->select();

            if (count($where) > 0) {
                $select->where($where);
            }

            if (count($columns) > 0) {
                $select->columns($columns);
            }

            if (count($order) > 0) {
                $select->order($order);
            }

            return $this->executeSelect($select);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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