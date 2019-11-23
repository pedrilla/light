<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class LoaderClassDoesNotExists
 * @package Light\Exception
 */
class LoaderClassDoesNotExists extends \Exception
{
    /**
     * LoaderClassDoesNotExists constructor.
     * @param string $className
     */
    public function __construct(string $className)
    {
        parent::__construct('LoaderClassDoesNotExists:' . $className, 500);
    }
}