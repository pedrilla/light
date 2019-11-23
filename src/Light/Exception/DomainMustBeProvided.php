<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class DomainMustBeProvided
 * @package Light\Exception
 */
class DomainMustBeProvided extends \Exception
{
    /**
     * DomainMustBeProvided constructor.
     */
    public function __construct()
    {
        parent::__construct('DomainMustBeProvided: ', 500);
    }
}