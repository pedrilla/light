<?php

declare(strict_types = 1);

namespace Light\Validator;

/**
 * Class ValidatorAbstract
 * @package Light\Validator
 */
abstract class ValidatorAbstract
{
    /**
     * @var bool
     */
    public $allowNull = false;

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    /**
     * @param bool $allowNull
     */
    public function setAllowNull(bool $allowNull): void
    {
        $this->allowNull = $allowNull;
    }

    /**
     * ValidatorAbstract constructor.
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
     * @return bool
     */
    public abstract function isValid($value) : bool;
}
