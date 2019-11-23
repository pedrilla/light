<?php

declare(strict_types = 1);

namespace Light\Form\Element;

/**
 * Class Checkbox
 * @package Light\Form\Element
 */
class Checkbox extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/checkbox';

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return (bool)parent::getValue();
    }
}