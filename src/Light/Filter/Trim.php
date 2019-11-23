<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class Trim
 * @package Light\Filter
 */
class Trim extends FilterAbstract
{
    /**
     * @param $value
     * @return mixed|string
     */
    public function filter($value)
    {
        return trim($value);
    }
}