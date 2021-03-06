<?php

namespace ModelGenerator\Core;

use Zend\Db\Adapter\Adapter;

/**
 * Class Generator
 *
 * @package ModelGenerator\Core
 */
class Generator
{

    /**
     * @var Generator
     */
    private static $__instance;

    /**
     * @var
     */
    private $adapter;

    /**
     * @var array
     */
    private $config = array();

    /**
     * ModelGenerator constructor.
     *
     * @param null $config
     *
     */
    public function __construct($config = null)
    {

        $defaults = include(__DIR__ . '/../../config/database.local.php');
        if(is_null($defaults) || empty($defaults)){
            // ZF2 local.php config
            $globalDefaults = include(__DIR__ . '/../../../../../config/autoload/global.php');
            $localDefaults = include(__DIR__ . '/../../../../../config/autoload/local.php');
            $defaults = array_replace_recursive($globalDefaults === false ? []: $globalDefaults, $localDefaults === false ? []: $localDefaults);
        }
        if ($defaults) {
            $this->addConfig($defaults);
        }

        if (!is_null($config) && !empty($config)) {
            $this->addConfig($config);
        }

        if (isset($this->getConfig()['db'])) {
            $adapter = new Adapter($this->getConfig()['db']);
            $this->setAdapter($adapter);
        }

        self::$__instance = $this;

    }

    /**
     * Return the web app singleton
     *
     * @return Generator
     */
    public static function app()
    {
        if (is_null(self::$__instance)) {
            self::$__instance = new Generator();
        }
        return self::$__instance;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param array $config
     *
     * @return Generator
     */
    public function addConfig($config)
    {
        $this->setConfig(array_merge_recursive($config, $this->getConfig()));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param Adapter $adapter
     *
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

}
