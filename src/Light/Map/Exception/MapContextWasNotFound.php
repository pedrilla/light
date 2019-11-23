<?php

declare(strict_types = 1);

namespace Light\Map\Exception;

/**
 * Class MapContextWasNotFound
 * @package Light\Map\Exception
 */
class MapContextWasNotFound extends \Exception
{
    /**
     * MapContextWasNotFound constructor.
     *
     * @param string $mapClassName
     * @param string $context
     */
    public function __construct(string $mapClassName, string $context)
    {
        parent::__construct("MapContextWasNotFound: " . $mapClassName . '::' . $context);
    }
}