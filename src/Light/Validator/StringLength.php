<?php

declare(strict_types = 1);

namespace Light\Validator;

/**
 * Class StringLength
 * @package Light\Validator
 */
class StringLength extends Number
{
    /**
     * @var string
     */
    public $encoding = 'UTF-8';

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $value = $value ?? '';

        if (empty($value) && $this->allowNull) {
            return true;
        }

        if ($this->min && $this->min > mb_strlen($value, $this->encoding)) {
            return false;
        }

        if ($this->max && $this->max < mb_strlen($value, $this->encoding)) {
            return false;
        }

        return true;
    }
}
