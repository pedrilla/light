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
        if (!isset(self::$_data[$key])) {
            throw new Exception\RegistryKeyIsNotExists($key);
        }

        return self::$_data[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function check(string $key)
    {
        return isset(self::$_data[$key]);
    }

    /**
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset(self::$_data[$key]);
    }
}
