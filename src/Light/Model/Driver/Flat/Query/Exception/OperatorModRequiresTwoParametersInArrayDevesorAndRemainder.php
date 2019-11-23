<?php

namespace Light\Model\Driver\Flat\Query\Exception;

/**
 * Class OperatorModRequiresTwoParametersInArrayDevesorAndRemainder
 * @package Light\Model\Driver\Flat\Query\Exception
 */
class OperatorModRequiresTwoParametersInArrayDevesorAndRemainder extends \Exception
{
    /**
     * OperatorModRequiresTwoParametersInArrayDevesorAndRemainder constructor.
     */
    public function __construct()
    {
        parent::__construct("OperatorModRequiresTwoParametersInArrayDevesorAndRemainder: ");
    }
}