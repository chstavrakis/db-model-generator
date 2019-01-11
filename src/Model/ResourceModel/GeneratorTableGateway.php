<?php

namespace ModelGenerator\Model\ResourceModel;

use ModelGenerator\Core\Generator;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class GeneratorTableGateway
 *
 * @package ModelGenerator\Model\ResourceModel
 */
class GeneratorTableGateway extends TableGateway
{

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * GeneratorTableGateway constructor.
     *
     * @param $tableName
     * @param $primaryKey
     * @param AdapterInterface $adapter
     */
    public function __construct($tableName, $primaryKey, $adapter = null)
    {
        $this->primaryKey = $primaryKey;

        if(is_null($adapter)){
            $adapter = Generator::app()->getAdapter();
        }

        parent::__construct($tableName, $adapter);
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