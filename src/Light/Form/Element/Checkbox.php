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
     * Checkbox constructor.
     * 
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = [])
    {
        $this->setAllowNull(true);
        parent::__construct($name, $options);
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return (bool)parent::getValue();
    }
}
