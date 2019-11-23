<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class RegistryKeyIsNotExists
 * @package Light\Exception
 */
class RegistryKeyIsNotExists extends \Exception
{
    /**
     * RegistryKeyIsNotExists constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct('RegistryKeyIsNotExists:' . $key, 500);
    }
}