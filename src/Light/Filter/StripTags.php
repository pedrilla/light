<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class StripTags
 * @package Light\Filter
 */
class StripTags extends FilterAbstract
{
    /**
     * @var array
     */
    public $allowableTags = [];

    /**
     * @return array
     */
    public function getAllowableTags(): array
    {
        return $this->allowableTags;
    }

    /**
     * @param array $allowableTags
     */
    public function setAllowableTags(array $allowableTags): void
    {
        $this->allowableTags = $allowableTags;
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function filter($value)
    {
        $allowableTags = [];

        foreach ($this->allowableTags as $allowableTag) {
            $allowableTags[] = '<' . $allowableTag . '>';
        }

        return strip_tags($value, implode(null, $allowableTags));
    }
}