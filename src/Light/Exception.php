<?php

declare(strict_types = 1);

namespace Light;

use Throwable;

/**
 * Class Exception
 * @package Light
 */
class Exception extends \Exception
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Exception constructor.
     *
     * @param array $errors
     * @param string $message
     * @param int $code
     */
    public function __construct(array $errors = [], string $message = "", int $code = 0)
    {
        $this->setErrors($errors);
        parent::__construct($message, $code);
    }
}
