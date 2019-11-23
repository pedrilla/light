<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class Uppercase
 * @package Light\Filter
 */
class Uppercase extends FilterAbstract
{
    /**
     * @param $value
     * @return mixed|string
     */
    public function filter($value)
    {
        return strtoupper($value);
    }
}