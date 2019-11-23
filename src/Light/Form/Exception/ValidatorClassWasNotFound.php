<?php

declare( strict_types = 1 );

namespace Light\Form\Exception;

/**
 * Class ValidatorClassWasNotFound
 * @package Light\Form\Exception
 */
class ValidatorClassWasNotFound extends \Exception
{
    /**
     * ValidatorClassWasNotFound constructor.
     * @param string|null $validatorClassName
     */
    public function __construct(string $validatorClassName = null)
    {
        parent::__construct('ValidatorClassWasNotFound: ' . $validatorClassName);
    }
}