<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class RequestMethodIsNotSupported
 * @package Light\Exception
 */
class RequestMethodIsNotSupported extends \Exception
{
    /**
     * RequestMethodIsNotSupported constructor.
     * @param string $method
     */
    public function __construct(string $method)
    {
        parent::__construct('RequestMethodIsNotSupported: ' . $method, 500);
    }
}