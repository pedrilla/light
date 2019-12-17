<?php

declare( strict_types = 1 );

namespace Light\Form\Exception;

/**
 * Class FilterClassWasNotFound
 * @package Light\Form\Exception
 */
class FilterClassWasNotFound extends \Exception
{
    /**
     * FilterClassWasNotFound constructor.
     * @param string|null $filterClassName
     */
    public function __construct(string $filterClassName = null)
    {
        parent::__construct('FilterClassWasNotFound: ' . $filterClassName);
    }
}
