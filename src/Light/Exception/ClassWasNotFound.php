<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class ClassWasNotFound
 * @package Light\Exception
 */
class ClassWasNotFound extends \Exception
{
    /**
     * ClassWasNotFound constructor.
     * @param string $className
     */
    public function __construct(string $className = null)
    {
        parent::__construct('ClassWasNotFound: ' . $className, 404);
    }
}