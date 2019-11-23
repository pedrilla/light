<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class Cookie
 * @package Light
 */
class Cookie
{
    /**
     * @var string|null
     */
    public static $namespace = null;

    /**
     * @return string|null
     */
    public static function getNamespace(): ?string
    {
        return self::$namespace;
    }

    /**
     * @param string|null $namespace
     */
    public static function setNamespace(?string $namespace): void
    {
        self::$namespace = $namespace;
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return bool
     */
    public static function set(string $name, $value) : bool
    {
        return self::_set($name, base64_encode(serialize($value)));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get(string $name)
    {
        $name = self::_getName($name);

        if ($value = self::_get($name)) {
            return unserialize(base64_decode($value));
        }

        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function remove(string $name)
    {
        $name = self::_getName($name);

        unset($_COOKIE[$name]);

        return setcookie($name, '', 1, '/');
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    private static function _set(string $name, string $value) : bool
    {
        $name = self::_getName($name);

        $_COOKIE[$name] = $value;
        return setcookie($name, $value, time() * 2, '/');
    }

    /**
     * @param string $name
     * @return null
     */
    private static function _get(string $name)
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * @param string $name
     * @return string
     */
    private static function _getName(string $name)
    {
        if (Front::getInstance()->getConfig()['light']['cookie'] ?? false) {
            self::setNamespace(Front::getInstance()->getConfig()['light']['cookie']);
        }

        if (!self::$namespace) {
            return $name;
        }

        return implode(':', [self::$namespace, $name]);
    }
}