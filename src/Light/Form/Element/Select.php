<?php

declare(strict_types = 1);

namespace Light\Form\Element;

use Light\Model;
use Light\Model\Driver\CursorAbstract;
use Light\Model\Driver\Flat\Cursor;

/**
 * Class Select
 * @package Light\Form\Element
 */
class Select extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/select';

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public $field = null;

    /**
     * @var string
     */
    public $optionsClassName = null;

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options): void
    {
        $this->options = $options;

        if (is_object($this->options)) {

            if (is_subclass_of($this->options, 'Light\\Model\\Driver\\CursorAbstract')) {

                $this->optionsClassName = get_class($this->options->getModel());
            }
        }
    }

    /**
     * @param string $field
     */
    public function setField(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function getNormalizedOptions()
    {
        if (!count($this->options)) {
            return [];
        }

        $options = [];

        if (is_object($this->options)) {

            if (is_subclass_of($this->options, 'Light\\Model\\Driver\\CursorAbstract')) {

                /** @var CursorAbstract $option */
                foreach ($this->options as $option) {
                    $options[$option['id']] = $option[$this->getField()];
                }
            }
        }

        else if (is_array($this->options) && gettype(array_keys($this->options)[0]) == 'int') {

            foreach ($this->options as $option) {
                $options[$option] = $option;
            }
        }

        else {
            $options = $this->options;
        }

        return $options;
    }

    /**
     * @return bool|int|Model|object|string|null
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($this->optionsClassName) {

            foreach ($this->options as $option) {

                if ($option['id'] == $value) {

                    return $option;
                }
            }
        }

        return $value;
    }

    /**
     * @return bool|int|object|string|null
     */
    public function getNormalizedValue()
    {
        $value = parent::getValue();

        if ($this->optionsClassName && !is_null($value)) {

            foreach ($this->options as $option) {

                if (is_string($value)) {
                    if ($option['id'] == $value) {
                        return $value;
                    }
                }

                if ($option['id'] == $value->id) {
                    return $option['id'];
                }
            }
        }

        return $value;
    }
}
