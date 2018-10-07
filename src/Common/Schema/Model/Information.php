<?php
namespace ModelGenerator\Common\Schema\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class Information extends AbstractTableGateway
{

    public $table = 'columns';

    protected $infoResults;

    public function __construct($dbParams)
    {
        $dbParams['database'] = 'information_schema';
        $this->adapter = new Adapter($dbParams);
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }
    
    public function load($where = array())
    {
        $columns = ['table_name', 'column_name', 'data_type', 'column_key'];
        $order = ['table_name', 'ordinal_position'];

        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'info' => $this->table
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
            $this->infoResults = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();

            return $this;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getInfoResults()
    {
        return $this->infoResults;
    }
}
