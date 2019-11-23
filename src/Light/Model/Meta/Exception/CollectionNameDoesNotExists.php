<?php

declare(strict_types = 1);

namespace Light\Model\Meta\Exception;

/**
 * Class CollectionNameDoesNotExists
 * @package Model\Meta\Exception
 */
class CollectionNameDoesNotExists extends \Exception
{
    /**
     * CollectionNameDoesNotExists constructor.
     * @param \Light\Model $model
     */
    public function __construct(\Light\Model $model)
    {
        parent::__construct("CollectionNameDoesNotExists: " . var_export($model, true));
    }
}