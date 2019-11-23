<?php

namespace Light\Model\Driver\Flat\Query\Exception;

/**
 * Class LogicalOperatorRequiresNonEmptyArray
 * @package Light\Model\Driver\Flat\Query\Exception
 */
class LogicalOperatorRequiresNonEmptyArray extends \Exception
{
    /**
     * LogicalOperatorRequiresNonEmptyArray constructor.
     * @param string $logicalOperator
     */
    public function __construct(string $logicalOperator)
    {
        parent::__construct("LogicalOperatorRequiresNonEmptyArray: " . $logicalOperator);
    }
}