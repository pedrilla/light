<?php

declare(strict_types = 1);

namespace Light\Validator;

/**
 * Class Email
 * @package Light\Validator
 */
class Email extends ValidatorAbstract
{
    /**
     * @param string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if (empty($value) && $this->allowNull) {
            return true;
        }

        $value = trim($value);

        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}