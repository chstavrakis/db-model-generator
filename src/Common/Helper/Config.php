<?php
namespace ModelGenerator\Common\Helper;


class Config
{
    /**
     * @var string
     */
    public static $moduleDir = '';

    /**
     * @var string
     */
    public static $vendorDir = '';

    /**
     * @return string
     */
    public static function getModelDir()
    {
        return self::getModuleDir() . '/src/Model';
    }

    /**
     * @return string
     */
    public static function getConfigDir()
    {
        return self::getModuleDir() . '/config';
    }

    /**
     * @return string
     */
    public static function getVendorDir()
    {
        return self::$vendorDir;
    }

    /**
     * @param string $vendorDir
     */
    public static function setVendorDir($vendorDir)
    {
        self::$vendorDir = $vendorDir;
    }

    /**
     * @return string
     */
    public static function getModuleDir()
    {
        return self::$moduleDir;
    }

    /**
     * @param string $moduleDir
     */
    public static function setModuleDir($moduleDir)
    {
        self::$moduleDir = $moduleDir;
    }
}