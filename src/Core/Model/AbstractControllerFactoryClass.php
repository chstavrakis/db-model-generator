<?php

namespace ModelGenerator\Core\Model;

use ModelGenerator\Common\Helper\Config;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class AbstractControllerClass
 *
 * @package ModelGenerator\Common\Schema\Model
 */
class AbstractControllerFactoryClass
{
    protected $namespace;
    protected $tableName;
    protected $controllerNamespace;
    /**
     * @var
     */
    protected $controllerNameCamelize;

    /**
     * AbstractModelClass constructor.
     * @param string $namespace
     * @param string $controllerNamespace
     */
    public function __construct($namespace, $controllerNamespace)
    {
        $this->namespace = $namespace;
        $this->controllerNamespace = $controllerNamespace;
        $this->clearControllerFactoryDirectory();
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
        return sprintf("%sControllerFactory",
            $this->controllerNameCamelize);
    }

    protected function getNamespace()
    {
        return $this->namespace;
    }


    protected function getUseClasses()
    {
        return sprintf('use %s\%sController;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use ModelGenerator\Model\%s;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;', $this->controllerNamespace, $this->controllerNameCamelize, $this->controllerNameCamelize);
    }

    protected function getImplementsClass()
    {
        return 'FactoryInterface';
    }

    protected function getContentClass()
    {
        $result = "/**
     * Create an object
     *
     * @param ContainerInterface \$container
     * @param string \$requestedName
     * @param null|array \$options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface \$container, \$requestedName, array \$options = null)
    {
        return (new ". $this->controllerNameCamelize ."Controller())->setModel((new ".$this->controllerNameCamelize."()));
    }";

        return $result;
    }

    /**
     *
     */
    protected function writeControllerFile()
    {
        $controllerDir = Config::getControllerFactoryDir();
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

    /**
     *
     */
    protected function clearControllerFactoryDirectory()
    {
        $modelDir = Config::getControllerFactoryDir();
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

class %s implements %s
{
    %s
}',         $this->getNamespace(),
            $this->getUseClasses(),
            $this->getControllerClassName(),
            $this->getImplementsClass(),
            $this->getContentClass()
        );
    }
}
