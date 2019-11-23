<?php

declare(strict_types=1);

namespace Light;

use Light\Exception\ClassWasNotFound;

/**
 * Class Loader
 * @package Light
 */
class Loader
{
    /**
     * @var array
     */
    private $_config = [];

    /**
     * Loader constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->_config = $config;

        $self = $this;

        spl_autoload_register(function($className) use ($config, $self) {

            $classFilePath = $self->getClassFilePath($className);

            if (!$classFilePath) {
                throw new ClassWasNotFound($classFilePath);
            }

            if (file_exists($classFilePath)) {
                require_once $classFilePath;
                return;
            }
        });
    }

    /**
     * @param string $namespace
     * @return string|null
     */
    public function getClassFilePath(string $namespace)
    {
        $namespace = explode('\\', $namespace);

        if ($namespace[0] == 'Light') {
            unset($namespace[0]);
            return implode('/', array_merge([realpath(__DIR__)], $namespace)) . '.php';
        }

        else if ($namespace[0] == $this->_config['light']['loader']['namespace']) {

            unset($namespace[0]);

            return implode('/', array_merge(
                [$this->_config['light']['loader']['path']],
                $namespace
            )).'.php';
        }
    }
}