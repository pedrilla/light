<?php

namespace Light\Model\Driver\Flat\Query\Exception;

/**
 * Class OperatorNinRequiresArray
 * @package Light\Model\Driver\Flat\Query\Exception
 */
class OperatorNinRequiresArray extends \Exception
{
    /**
     * OperatorNinRequiresArray constructor.
     * @param mixed $operatorValue
     */
    public function __construct($operatorValue)
    {
        parent::__construct("OperatorNinRequiresArray: " . $operatorValue);
    }
}