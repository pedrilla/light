<?php

namespace Light\Model\Driver\Exception;

/**
 * Class IndexOutOfRange
 * @package Light\Model\Exception
 */
class IndexOutOfRange extends \Exception
{
    /**
     * IndexOutOfRange constructor.
     *
     * @param int $index
     * @param int $maxValue
     */
    public function __construct(int $index, int $maxValue)
    {
        parent::__construct("IndexOutOfRange: " . $index . ', max value is ' . $maxValue);
    }
}