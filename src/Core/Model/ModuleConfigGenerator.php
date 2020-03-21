<?php

namespace ModelGenerator\Core\Model;


use ModelGenerator\Common\Helper\Config;
use ModelGenerator\Core\Exceptions\GeneratorException;

/**
 * Class ModelGenerator
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class ModuleConfigGenerator
{
    const MODULE_CONFIG_FILE_NAME = 'module.config.php';

    /**
     * @var InformationSchema
     */
    private $information;
    /**
     * @var
     */
    private $schemaResult;
    /**
     * @var string
     */
    private $factoryNamespace;
    /**
     * @var string
     */
    private $controllerNamespace;

    /**
     * ModelGenerator constructor.
     *
     * @param InformationSchema $information
     * @param string $factoryNamespace
     * @param string $controllerNamespace
     */
    public function __construct(InformationSchema $information, $factoryNamespace = 'ModelGenerator\Factory', $controllerNamespace= 'Controller\V1')
    {
        $this->information = $information;
        $this->factoryNamespace = $factoryNamespace;
        $this->controllerNamespace = $controllerNamespace;
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->getSchemaResult();
        $this->clearModuleConfigDirectory();

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
            echo "Create Module Config File: " . PHP_EOL;
            $this->writeModelFile();

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

    /**
     *
     */
    protected function writeModelFile()
    {
        $modelDir = Config::getModuleConfigDir();
        if (!file_exists($modelDir)) {
            mkdir($modelDir, 0777, true);
        }
        $ourFileName = $modelDir . sprintf("/%s", static::MODULE_CONFIG_FILE_NAME);
        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
        fwrite($ourFileHandle, $this->getFileContent());
        fclose($ourFileHandle);
    }

    /**
     * @return string
     */
    protected function getFileContent()
    {
        $retStartVal = "<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api;


use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api'
                ],
                'may_terminate' => true,
                'child_routes' => [
                    ";

        foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
            $activeFilter = Config::tableHasActiveColumn($tableColumns);
            $camelizeName = $this->camelize($tableName);
            $apiUrlName = $this->urlize($tableName);
            $retStartVal .= "'v1-$apiUrlName' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/v1/".$apiUrlName."[/:id]',
                            'defaults' => [
                                'controller' => ".$this->controllerNamespace."\\".$camelizeName."Controller::class,
                            ],
                            'constraints' => array(
                                'id'     => '[0-9]+',
                            )
                        ]
                    ],
                    ";
            if($activeFilter) {
                $retStartVal .= "'v1-$apiUrlName-active' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/v1/$apiUrlName/active',
                            'defaults' => [
                                'controller' => " . $this->controllerNamespace . "\\" . $camelizeName . "Controller::class,
                                'action' => 'active'
                            ],
                            'constraints' => array(
                                'id'     => '[0-9]+',
                            )
                        ]
                    ],
                    ";
            }
        }


        $retEndVal = "                ]
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            ";
        foreach ($this->getSchemaResult() as $tableName => $tableColumns) {
            $camelizeName = $this->camelize($tableName);
            $retEndVal .= $this->controllerNamespace."\\".$camelizeName."Controller::class => '".$this->factoryNamespace."\\".$camelizeName."ControllerFactory',
            ";
        }

        $retEndVal .="],
    ],
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
];";

        return $retStartVal . $retEndVal;
    }

    /**
     *
     */
    protected function clearModuleConfigDirectory()
    {
        //TODO
        $modelDir = Config::getModuleConfigDir();
        $files = glob($modelDir . '/*');
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
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
    protected function urlize($input, $search = '_', $replace = '-')
    {
        return str_replace($search, $replace, $input);
    }

}
