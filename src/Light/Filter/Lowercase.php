<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class Lowercase
 * @package Light\Filter
 */
class Lowercase extends FilterAbstract
{
    /**
     * @param $value
     * @return mixed|string
     */
    public function filter($value)
    {
        return strtolower($value);
    }
}