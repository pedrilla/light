<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class Registry
 */
final class Registry
{
    /**
     * @var array
     */
    private static $_data = [];

    /**
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value)
    {
        self::$_data[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception\RegistryKeyIsNotExists
     */
    public static function get(string $key)
    {
        if (!isset(self::$_data)) {
            throw new Exception\RegistryKeyIsNotExists($key);
        }

        return self::$_data[$key];
    }
}