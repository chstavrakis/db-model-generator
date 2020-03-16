<?php

namespace ModelGenerator\Core\Model;


use ModelGenerator\Core\Exceptions\GeneratorException;

/**
 * Class ModelGenerator
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class ControllerGenerator
{
    /**
     * @var AbstractControllerClass
     */
    private $abstractControllerClass;
    /**
     * @var InformationSchema
     */
    private $information;
    /**
     * @var
     */
    private $schemaResult;

    /**
     * ControllerGenerator constructor.
     *
     * @param InformationSchema $information
     * @param string $namespace
     * @param bool $isRestController
     */
    public function __construct(InformationSchema $information, $namespace = 'Application\Controller', $isRestController = false)
    {
        $this->abstractControllerClass = new AbstractControllerClass($namespace, $isRestController);
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
     * @return $this
     */
    public function create()
    {
        try {

            if (empty($this->getSchemaResult())) {
                throw new GeneratorException('Schema is Empty.');
            }

            foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
                echo "Create Controllers Process - Table: $tableName" . PHP_EOL;
                $this->abstractControllerClass
                    ->initClass($tableName)
                    ->saveClassFile();
            }

        } catch (GeneratorException $e) {
            echo "ERROR:: " . $e->getMessage();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getSchemaResult()
    {
        if (!$this->schemaResult) {
            $infoResults = $this->information->getInfoResults();
            foreach ($infoResults ?? [] as $item) {
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
