<?php

namespace Light\Model\Driver\Flat\Exception;

/**
 * Class OpenSSLFunctionDoesNotExists
 * @package Light\Model\Driver\Flat\Exception
 */
class OpenSSLFunctionDoesNotExists extends \Exception
{
    /**
     * OpenSSLFunctionDoesNotExists constructor.
     *
     * @param string $opensslFunction
     */
    public function __construct(string $opensslFunction)
    {
        parent::__construct("OpenSSLFunctionDoesNotExists: " . $opensslFunction);
    }
}