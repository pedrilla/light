<?php

declare( strict_types = 1 );

namespace Light;

/**
 * Class Config
 * @package Light
 */
class Config
{
    /**
     * @var array
     */
    private static $_config = null;

    /**
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        self::$_config = $config;
    }

    /**
     * @return array
     */
    public static function getConfig()
    {
        if (!self::$_config) {
            self::$_config = Front::getInstance()->getConfig()['light']['db'];
        }

        return self::$_config;
    }
}