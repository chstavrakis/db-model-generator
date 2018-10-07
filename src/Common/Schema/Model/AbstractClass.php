<?php
namespace ModelGenerator\Common\Schema\Model;


class AbstractClass
{
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
        $this->prepareFile();
        $this->writeFile();
    }

    protected function prepareFile()
    {
        //TODO
    }

    protected function writeFile()
    {
        //TODO
    }

    protected function clearModelDirectory()
    {
        //TODO
        $files = glob('src/Model/*');
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
}
