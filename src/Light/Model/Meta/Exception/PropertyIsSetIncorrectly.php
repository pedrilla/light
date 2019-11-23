<?php

declare(strict_types = 1);

namespace Light\Model\Meta\Exception;

/**
 * Class PropertyIsSetIncorrectly
 * @package Meta\Exception
 */
class PropertyIsSetIncorrectly extends \Exception
{
    /**
     * PropertyIsSetIncorrectly constructor.
     * @param \Light\Model $model
     * @param string $line
     */
    public function __construct(\Light\Model $model, string $line)
    {
        parent::__construct("PropertyIsSetIncorrectly: line: '" . $line . "', model: " . var_export($model, true));
    }
}