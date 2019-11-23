<?php

namespace Light\Model\Driver\Flat\Query\Exception;

/**
 * Class UnknownOperator
 * @package Light\Model\Driver\Flat\Query\Exception
 */
class UnknownOperator extends \Exception
{
    /**
     * UnknownOperator constructor.
     * @param mixed $operator
     */
    public function __construct($operator)
    {
        parent::__construct("UnknownOperator: " . $operator);
    }
}