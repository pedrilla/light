<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class RouterVarMustBeProvided
 * @package Light\Exception
 */
class RouterVarMustBeProvided extends \Exception
{
    /**
     * RouterVarMustBeProvided constructor.
     * @param string $var
     */
    public function __construct(string $var)
    {
        parent::__construct('RouterVarMustBeProvided: ' . $var, 500);
    }
}