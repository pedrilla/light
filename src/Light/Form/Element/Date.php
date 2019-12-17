<?php

declare(strict_types = 1);

namespace Light\Form\Element;

/**
 * Class Date
 * @package Light\Form\Element
 */
class Date extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/date';

    /**
     * @var string
     */
    public $format = 'YYYY-MM-DD';

    /**
     * @var string
     */
    public $phpFormat = 'Y-m-d';

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getPhpFormat(): string
    {
        return $this->phpFormat;
    }

    /**
     * @param string $phpFormat
     */
    public function setPhpFormat(string $phpFormat): void
    {
        $this->phpFormat = $phpFormat;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        $value = parent::getValue();

        if (empty($value)) {
            $value = time();
        }

        if (is_string($value)) {
            return strtotime($value);
        }

        return $value;
    }
}
