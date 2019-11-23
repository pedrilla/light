<?php

declare(strict_types=1);

namespace Light\Model\Exception;

/**
 * Class CallUndefinedMethod
 * @package Light\Model\Exception
 */
class CallUndefinedMethod extends \Exception
{
    /**
     * CallUndefinedMethod constructor.
     *
     * @param string $className
     * @param string $methodName
     */
    public function __construct(string $className, string $methodName)
    {
        parent::__construct('CallUndefinedMethod: ' . $className . '::' . $methodName);
    }
}