<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class BootstrapAbstract
 * @package Light
 */
abstract class BootstrapAbstract
{
    /**
     * @var array
     */
    private $_config = [];

    /**
     * @return array
     */
    final public function getConfig(): array
    {
        return $this->_config;
    }

    /**
     * @param array $config
     */
    final public function setConfig(array $config)
    {
        $this->_config = $config;
    }
}