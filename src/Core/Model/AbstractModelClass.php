<?php

namespace ModelGenerator\Core\Model;

use ModelGenerator\Common\Helper\Config;

/**
 * Class AbstractModelClass
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class AbstractModelClass
{
    /**
     *
     */
    const MODEL_DIRECTORY = 'src/Model';
    /**
     *
     */
    const ABSTRACT_CLASS_TABLE_PRIMARY_KEY = 'PRI';

    /**
     * @var
     */
    protected $tableName;
    /**
     * @var
     */
    protected $tableColumns;
    /**
     * @var
     */
    protected $tablePrimaryKey;
    /**
     * @var
     */
    protected $tableNameCamelize;

    /**
     * AbstractModelClass constructor.
     */
    public function __construct()
    {
        $this->clearModelDirectory();
    }


    /**
     * @param $tableName
     * @param $tableColumns
     *
     * @return $this
     */
    public function initClass($tableName, $tableColumns)
    {
        //TODO: Check for parameters validity
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns['columns'];
        $this->tablePrimaryKey = $this->getPrimaryKey();
        $this->tableNameCamelize = $this->camelize($this->tableName);

        return $this;
    }



    public function saveClassFile()
    {
        $this->writeModelFile();

        return $this;
    }


    /**
     *
     */
    protected function writeModelFile()
    {
        $modelDir = Config::getModelDir();
        if (!file_exists($modelDir)) {
            mkdir($modelDir, 0777, true);
        }
        $ourFileName = $modelDir . sprintf("/%s.php", $this->tableNameCamelize);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fwrite($ourFileHandle, $this->getClassTemplate());
        fclose($ourFileHandle);
    }

    /**
     * @param        $input
     * @param string $separator
     *
     * @return mixed
     */
    protected function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     *
     */
    protected function clearModelDirectory()
    {
        //TODO
        $modelDir = Config::getModelDir();
        $files = glob($modelDir . '/*');
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
    }

    /**
     * @return mixed
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    protected function getPrimaryKey()
    {
        $primaryKey = '';
        foreach ($this->getTableColumns() as $column) {
            if ($column['column_key'] === self::ABSTRACT_CLASS_TABLE_PRIMARY_KEY) {
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

    /**
     * @return string
     */
    protected function getClassTemplate()
    {

        return sprintf('<?php
namespace ModelGenerator\Model;

use ModelGenerator\Core\TableGateway\GeneratorTableGateway;

class %s extends GeneratorTableGateway
{
    
    public function __construct()
    {
        parent::__construct(\'%s\',\'%s\');
    }
    
}',         $this->tableNameCamelize,
            $this->getTableName(),
            $this->getPrimaryKey()
        );
    }
}
