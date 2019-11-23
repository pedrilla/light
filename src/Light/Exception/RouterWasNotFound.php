<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class RouterWasNotFound
 * @package Light\Exception
 */
class RouterWasNotFound extends \Exception
{
    /**
     * RouterWasNotFound constructor.
     * @param string $router
     */
    public function __construct(string $router)
    {
        parent::__construct('RouterWasNotFound: ' . $router, 404);
    }
}