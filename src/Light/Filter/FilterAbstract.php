<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class FilterAbstract
 */
abstract class FilterAbstract
{
    /**
     * FilterAbstract constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $value) {

            if (is_callable([$this, 'set' . ucfirst($name)])) {
                call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public abstract function filter($value);
}