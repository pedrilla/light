<?php

declare(strict_types=1);

namespace Light\Exception;

/**
 * Class Stop
 * @package Light\Exception
 */
class Stop extends \Exception
{
    /**
     * Stop constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}