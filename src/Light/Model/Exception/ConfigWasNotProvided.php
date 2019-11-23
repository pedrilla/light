<?php

declare(strict_types=1);

namespace Light\Model\Exception;

/**
 * Class CallUndefinedMethod
 * @package Light\Model\Exception
 */
class ConfigWasNotProvided extends \Exception
{
    /**
     * CallUndefinedMethod constructor.
     */
    public function __construct()
    {
        parent::__construct("ConfigWasNotProvided");
    }
}