<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class ControllerClassWasNotFound
 * @package Light\Exception
 */
class ControllerClassWasNotFound extends \Exception
{
    /**
     * ControllerClassWasNotFound constructor.
     * @param string $controllerClassName
     */
    public function __construct(string $controllerClassName)
    {
        parent::__construct('ControllerClassWasNotFound: ' . $controllerClassName, 404);
    }
}