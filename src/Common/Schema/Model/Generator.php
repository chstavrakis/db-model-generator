<?php

namespace ModelGenerator\Common\Schema\Model;


/**
 * Class Generator
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class Generator
{
    /**
     * @var AbstractClass
     */
    private $abstractClass;
    /**
     * @var mixed
     */
    private $infoResult;
    /**
     * @var
     */
    private $schemaResult;

    /**
     * Generator constructor.
     *
     * @param Information $information
     */
    public function __construct(Information $information)
    {
        $this->abstractClass = new AbstractClass();
        $this->infoResult = $information->getInfoResults();
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->getSchemaResult();
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function create()
    {
        try {

            if (empty($this->getSchemaResult())) {
                throw new \Exception('Schema is Empty.');
            }

            foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
                echo "Create Models Process - Table: $tableName" . PHP_EOL;
                $this->abstractClass->initClass($tableName, $tableColumns);
                $this->abstractClass->createClassFile();
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    protected function getSchemaResult()
    {
        if (!$this->schemaResult) {
            foreach ($this->infoResult as $item) {
                $this->schemaResult[$item['table_name']]['columns'][] = [
                    'name' => $item['column_name'],
                    'data_type' => $item['data_type'],
                    'column_key' => $item['column_key']
                ];
            }
        }
        return $this->schemaResult;
    }
}
