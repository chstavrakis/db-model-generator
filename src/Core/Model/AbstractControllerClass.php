<?php

namespace ModelGenerator\Core\Model;

use ModelGenerator\Common\Helper\Config;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class AbstractControllerClass
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class AbstractControllerClass
{
    protected $namespace;
    protected $tableName;
    protected $isRestController;
    /**
     * @var
     */
    protected $controllerNameCamelize;

    /**
     * AbstractModelClass constructor.
     * @param string $namespace
     * @param bool $isRestController
     */
    public function __construct($namespace = 'Application\Controller', $isRestController = false)
    {
        $this->namespace = $namespace;
        $this->isRestController = $isRestController;
        $this->clearControllerDirectory();
    }



    public function initClass($tableName)
    {
        $this->tableName = $tableName;
        $this->controllerNameCamelize = $this->camelize($tableName);
        return $this;
    }



    public function saveClassFile()
    {
        $this->writeControllerFile();

        return $this;
    }

    protected function getControllerClassName()
    {
        return sprintf("%sController",
            $this->controllerNameCamelize);
    }

    protected function getNamespace()
    {
        return $this->namespace;
    }

    protected function isRestController()
    {
        return $this->isRestController;
    }

    protected function getUseClasses()
    {
        return $this->isRestController() ?
            'use Api\Controller\AbstractRestfulJsonController;'. PHP_EOL .'use Zend\View\Model\JsonModel;' :
            'use Zend\Mvc\Controller\AbstractActionController;';
    }

    protected function getExtendsClass()
    {
        return $this->isRestController() ?
            'AbstractRestfulJsonController' :
            'AbstractActionController';
    }

    protected function getContentClass()
    {
        $apiUrlName = $this->urlize($this->tableName);
        $controllerName = $this->controllerNameCamelize;
        $lowControllerName = lcfirst($this->controllerNameCamelize);
        $result = '';
        if($this->isRestController()){
            $result = "
    /**
     * @SWG\Get(
     *     path=\"/api/v1/$apiUrlName\",
     *     summary=\"List all $lowControllerName\s\",
     *     operationId=\"getList\",
     *     tags={\"$controllerName\"},
     *     @SWG\Response(
     *         response=200,
     *         description=\"An paged array of $lowControllerName\s\",
     *         @SWG\Header(header=\"x-next\", type=\"string\", description=\"A link to the next page of responses\")
     *     ),
     *     @SWG\Response(
     *         response=\"default\",
     *         description=\"unexpected error\"
     *     )
     * )
     */
    public function getList()
    {
        return new JsonModel(\$this->getModel()->load(['purge' => '0']));
    }

    /**
     * @SWG\Get(
     *     path=\"/api/v1/$apiUrlName/active\",
     *     summary=\"List all active $lowControllerName\",
     *     operationId=\"active\",
     *     tags={\"$controllerName\"},
     *     @SWG\Response(
     *         response=200,
     *         description=\"An paged array of boilers\",
     *         @SWG\Header(header=\"x-next\", type=\"string\", description=\"A link to the next page of responses\")
     *     ),
     *     @SWG\Response(
     *         response=\"default\",
     *         description=\"unexpected error\"
     *     )
     * )
     */
    public function activeAction()
    {
        return new JsonModel(\$this->getModel()->load(['purge' => '0', 'status' => '1']));
    }

    /**
     * @SWG\Get(
     *     path=\"/api/v1/$apiUrlName/{id}\",
     *     summary=\"Get a $lowControllerName by id\",
     *     operationId=\"get\",
     *     tags={\"$controllerName\"},
     *     @SWG\Parameter(
     *         name=\"id\",
     *         in=\"path\",
     *         description=\"How many items to return at one time (max 100)\",
     *         required=true,
     *         type=\"integer\",
     *         minimum=1
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description=\"A boiler\",
     *         @SWG\Header(header=\"x-next\", type=\"string\", description=\"A link to the next page of responses\")
     *     ),
     *     @SWG\Response(
     *         response=\"default\",
     *         description=\"unexpected error\"
     *     )
     * )
     */
    public function get(\$id)
    {
        return new JsonModel(\$this->getModel()->loadById(\$id));
    }

    /**
     * @SWG\Put(
     *     path=\"/api/v1/$apiUrlName/{id}\",
     *     summary=\"Update a $lowControllerName by id\",
     *     operationId=\"update\",
     *     tags={\"$controllerName\"},
     *     @SWG\Parameter(
     *         name=\"id\",
     *         in=\"path\",
     *         description=\"How many items to return at one time (max 100)\",
     *         required=true,
     *         type=\"integer\",
     *         minimum=1
     *     ),
     *     @SWG\Parameter(
     *         name=\"data\",
     *         in=\"body\",
     *         description=\"How many items to return at one time (max 100)\",
     *         required=true,
     *         type=\"json\",
     *         @SWG\Schema(
     *              @SWG\Property(property=\"status\", type=\"string\")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description=\"A boiler\",
     *         @SWG\Header(header=\"x-next\", type=\"string\", description=\"A link to the next page of responses\")
     *     ),
     *     @SWG\Response(
     *         response=\"default\",
     *         description=\"unexpected error\"
     *     )
     * )
     */
    public function update(\$id, \$data)
    {
        \$resultSet = \$this->getModel()->updateById(\$id, \$data);

        return new JsonModel(['success' => true]);
    }

    /**
     * @SWG\Post(
     *     path=\"/api/v1/$apiUrlName\",
     *     summary=\"Create a new $lowControllerName\",
     *     operationId=\"create\",
     *     tags={\"$controllerName\"},
     *     @SWG\Parameter(
     *         name=\"data\",
     *         in=\"body\",
     *         description=\"How many items to return at one time (max 100)\",
     *         required=true,
     *         type=\"json\",
     *         @SWG\Schema(
     *              @SWG\Property(property=\"sync\", type=\"integer\"),
     *              @SWG\Property(property=\"purge\", type=\"integer\"),
     *              @SWG\Property(property=\"status\", type=\"integer\"),
     *              @SWG\Property(property=\"fired_status\", type=\"integer\"),
     *              @SWG\Property(property=\"name\", type=\"string\"),
     *              @SWG\Property(property=\"node_id\", type=\"integer\"),
     *              @SWG\Property(property=\"node_child_id\", type=\"integer\"),
     *              @SWG\Property(property=\"hysteresis_time\", type=\"integer\"),
     *              @SWG\Property(property=\"max_operation_time\", type=\"integer\"),
     *              @SWG\Property(property=\"datetime\", type=\"string\"),
     *              @SWG\Property(property=\"gpio_pin\", type=\"integer\")
     *          )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description=\"Success create\",
     *         @SWG\Schema(
     *              @SWG\Property(property=\"success\", type=\"boolean\"),
     *              @SWG\Property(property=\"id\", type=\"integer\"),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=\"default\",
     *         description=\"unexpected error\"
     *     )
     * )
     */
    public function create(\$data)
    {
        \$this->getModel()->insert(\$data);
        return new JsonModel([
            'success' => true,
            'id' => \$this->getModel()->getLastInsertValue()
        ]);
    }
            ";
        }

        return $result;

    }

    /**
     *
     */
    protected function writeControllerFile()
    {
        $controllerDir = Config::getControllerDir();
        if (!file_exists($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }
        $ourFileName = $controllerDir . sprintf("/%s.php", $this->getControllerClassName());
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

    protected function urlize($input, $search = '_', $replace = '-')
    {
        return str_replace($search, $replace, $input);
    }

    /**
     *
     */
    protected function clearControllerDirectory()
    {
        //TODO
        $modelDir = Config::getControllerDir();
        $files = glob($modelDir . '/*');
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
    }

    /**
     * @return string
     */
    protected function getClassTemplate()
    {

        return sprintf('<?php
namespace %s;

%s

class %s extends %s
{
    %s
}',         $this->getNamespace(),
            $this->getUseClasses(),
            $this->getControllerClassName(),
            $this->getExtendsClass(),
            $this->getContentClass()
        );
    }
}
