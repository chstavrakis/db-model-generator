<?php
namespace ModelGenerator\Common\Schema\Model;


class Generator
{
    private $abstractClass;
    private $infoResult;
    private $schemaResult;

    public function __construct(Information $information)
    {
        $this->abstractClass = new AbstractClass();
        $this->infoResult  = $information->getInfoResults();
    }

    public function init()
    {
        $this->getSchemaResult();
        return $this;
    }

    public function create()
    {
        try {

            if(empty($this->getSchemaResult())){
                throw new \Exception('Schema is Empty.');
            }

            foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
                echo "Create Models Process - Table: $tableName". PHP_EOL;
                $this->abstractClass->initClass($tableName, $tableColumns);
                $this->abstractClass->createClassFile();
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function getSchemaResult()
    {
        if(!$this->schemaResult){
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
