<?php

declare(strict_types = 1);

namespace Light\Exception;

/**
 * Class ActionMethodWasNotFound
 * @package Light\Exception
 */
class RouterDomainWasNotFound extends \Exception
{
    /**
     * RouterDomainWasNotFound constructor.
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        parent::__construct('RouterDomainWasNotFound: ' . $domain, 404);
    }
}