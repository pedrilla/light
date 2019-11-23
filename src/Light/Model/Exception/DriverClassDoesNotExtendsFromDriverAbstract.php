<?php

namespace Light\Model\Exception;

/**
 * Class DriverClassDoesNotExtendsFromDriverAbstract
 * @package Light\Model\Exception
 */
class DriverClassDoesNotExtendsFromDriverAbstract extends \Exception
{
    /**
     * DriverClassDoesNotExtendsFromDriverAbstract constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        parent::__construct("DriverClassDoesNotExtendsFromDriverAbstract: " . $className);
    }
}