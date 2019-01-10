<?php

namespace ModelGenerator\Common\Schema\Model;

use ModelGenerator\Common\Helper\Config;

/**
 * Class AbstractClass
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class AbstractClass
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
     * AbstractClass constructor.
     */
    public function __construct()
    {
        $this->clearModelDirectory();
    }

    /**
     * @param $tableName
     * @param $tableColumns
     */
    public function initClass($tableName, $tableColumns)
    {
        //TODO: Check for parameters validity
        $this->tableName = $tableName;
        $this->tableColumns = $tableColumns['columns'];
        $this->tablePrimaryKey = $this->getPrimaryKey();
        $this->tableNameCamelize = $this->camelize($this->tableName);
    }

    /**
     *
     */
    public function createClassFile()
    {
        $this->writeModelFile();
    }

    /**
     *
     */
    protected function writeModelFile()
    {
        $modelDir = Config::getModelDir();
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
            
            use ModelGenerator\Core\Generator;
            use ModelGenerator\Model\ResourceModel\TableGateway;
            
            class %s extends TableGateway
            {
                public function __construct()
                {
                    $adapter = Generator::app()->getAdapter();
                    parent::__construct($adapter);
                }
                
                protected function _construct()
                {
                    parent::_init(\'%s\',\'%s\');
                }
                
            }',
            $this->tableNameCamelize,
            $this->getTableName(),
            $this->getPrimaryKey()
        );
    }
}
