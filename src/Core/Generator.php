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
     * Generator constructor.
     *
     * @param null $config
     *
     */
    public function __construct($config = null)
    {
        if (!is_null(self::$__instance)) {
            return self::$__instance;
        }

        $defaults = include(__DIR__ . '/../../config/database.local.php');
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
