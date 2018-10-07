<?php
namespace ModelGenerator\Common\Schema\Model;


class AbstractClass
{
    const MODEL_DIRECTORY = 'src/Model';
    const ABSTRACT_CLASS_TABLE_PRIMARY_KEY = 'PRI';

    protected $tableName;
    protected $tableColumns;
    protected $tablePrimaryKey;

    public function __construct()
    {
        $this->clearModelDirectory();
    }

    public function initClass($tableName, $tableColumns)
    {
        //TODO: Check for parameters validity
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns['columns'];
        $this->tablePrimaryKey = $this->getPrimaryKey();
    }

    public function createClassFile()
    {
        $this->writeFile();
    }

    protected function writeFile()
    {
        $ourFileName = self::MODEL_DIRECTORY .sprintf("/%s.php", $this->camelize($this->tableName));
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fwrite($ourFileHandle, $this->getClassTemplate());
        fclose($ourFileHandle);
    }

    function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    protected function clearModelDirectory()
    {
        //TODO
        $files = glob(self::MODEL_DIRECTORY.'/*');
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    protected function getPrimaryKey()
    {
        $primaryKey = '';
        foreach ($this->getTableColumns() as $column) {
            if($column['column_key'] === self::ABSTRACT_CLASS_TABLE_PRIMARY_KEY){
                $primaryKey = $column['name'];
            }
        }
        return $primaryKey;
    }

    /**
     * @return mixed
     */
    protected function getTableColumns()
    {
        return $this->tableColumns;
    }

    protected function getClassTemplate()
    {
        return sprintf('<?php
namespace ModelGenerator\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class %s extends AbstractTableGateway
{

    public $table = \'%s\';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function load($where = array(), $columns = array(), $order = array())
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                \'main_table\' => $this->table
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
            $result = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }
}',$this->camelize($this->tableName), $this->tableName );
    }
}
