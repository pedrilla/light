<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class ActionMethodIsReserved
 * @package Light\Exception
 */
class ActionMethodIsReserved extends \Exception
{
    /**
     * ActionMethodIsReserved constructor.
     * @param string $actionClassName
     */
    public function __construct(string $actionClassName)
    {
        parent::__construct('ActionMethodIsReserved: ' . $actionClassName, 404);
    }
}