<?php

declare(strict_types = 1);

namespace Light\Model\Meta\Exception;

/**
 * Class CollectionCantBeWithoutPrimary
 * @package Model\Meta\Exception
 */
class CollectionCantBeWithoutPrimary extends \Exception
{
    /**
     * CollectionCantBeWithoutPrimary constructor.
     * @param \Light\Model $model
     */
    public function __construct(\Light\Model $model)
    {
        parent::__construct("CollectionCantBeWithoutPrimary: " . var_export($model, true));
    }
}