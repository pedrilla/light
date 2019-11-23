<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class ActionMethodWasNotFound
 * @package Light\Exception
 */
class ActionMethodWasNotFound extends \Exception
{
    /**
     * ActionMethodWasNotFound constructor.
     * @param string $actionClassName
     */
    public function __construct(string $actionClassName)
    {
        parent::__construct('ActionMethodWasNotFound: ' . $actionClassName, 404);
    }
}