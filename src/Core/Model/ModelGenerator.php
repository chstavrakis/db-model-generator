<?php

namespace ModelGenerator\Core\Model;


use ModelGenerator\Core\Exceptions\GeneratorException;

/**
 * Class ModelGenerator
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class ModelGenerator
{
    /**
     * @var AbstractModelClass
     */
    private $abstractModelClass;
    /**
     * @var InformationSchema
     */
    private $information;
    /**
     * @var
     */
    private $schemaResult;

    /**
     * ModelGenerator constructor.
     *
     * @param InformationSchema $information
     */
    public function __construct(InformationSchema $information)
    {
        $this->abstractModelClass = new AbstractModelClass();
        $this->information = $information;
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
     * @throws GeneratorException
     */
    public function create()
    {
        try {

            if (empty($this->getSchemaResult())) {
                throw new GeneratorException('Schema is Empty.');
            }

            foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
                echo "Create Models Process - Table: $tableName" . PHP_EOL;
                $this->abstractModelClass->initClass($tableName, $tableColumns);
                $this->abstractModelClass->createClassFile();
            }

        } catch (GeneratorException $e) {
            throw new GeneratorException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    protected function getSchemaResult()
    {
        if (!$this->schemaResult) {
            $infoResults = $this->information->getInfoResults();
            foreach ($infoResults as $item) {
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
