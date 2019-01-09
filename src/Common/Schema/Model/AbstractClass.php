<?php
namespace ModelGenerator\Common\Schema\Model;


use ModelGenerator\Common\Helper\Config;

/**
 *
 */
class AbstractClass
{
    const MODEL_DIRECTORY = 'src/Model';
    const ABSTRACT_CLASS_TABLE_PRIMARY_KEY = 'PRI';

    protected $tableName;
    protected $tableColumns;
    protected $tablePrimaryKey;
    protected $tableNameCamelize;

    public function __construct()
    {
        $this->clearModelDirectory();
        $this->clearResourceModelDirectory();
    }

    public function initClass($tableName, $tableColumns)
    {
        //TODO: Check for parameters validity
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns['columns'];
        $this->tablePrimaryKey = $this->getPrimaryKey();
        $this->tableNameCamelize = $this->camelize($this->tableName);
    }

    public function createClassFile()
    {
        $this->writeModelFile();
        $this->writeResourceModelFile();
    }

    protected function writeModelFile()
    {
        $modelDir = Config::getModelDir();
        $ourFileName = $modelDir .sprintf("/%s.php", $this->tableNameCamelize);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fwrite($ourFileHandle, $this->getClassTemplate());
        fclose($ourFileHandle);
    }

    protected function writeResourceModelFile()
    {
        $modelDir = Config::getModelDir();
        $ourFileName = $modelDir .sprintf("/ResourceModel/%s.php", $this->tableNameCamelize);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fwrite($ourFileHandle, $this->getResourceClassTemplate());
        fclose($ourFileHandle);
    }

    function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    protected function clearModelDirectory()
    {
        //TODO
        $modelDir = Config::getModelDir();
        $files = glob($modelDir.'/*');
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    protected function clearResourceModelDirectory()
    {
        //TODO
        $modelDir = Config::getModelDir();
        $files = glob($modelDir.'/ResourceModel/*');
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

    protected function getResourceClassTemplate()
    {
        return sprintf('<?php
namespace ModelGenerator\Model\ResourceModel;

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
}', $this->tableNameCamelize, $this->tableName );
    }

    protected function getClassTemplate()
    {

        return sprintf('<?php
namespace ModelGenerator\Model;


class %s extends AbstractModel
{


    public function __construct()
    {
       $this->setResourceModel(%s);
    }

    
}', $this->tableNameCamelize, $this->tableNameCamelize );
    }
}
