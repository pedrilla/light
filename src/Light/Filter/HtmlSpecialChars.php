<?php

declare(strict_types = 1);

namespace Light\Filter;

/**
 * Class HtmlSpecialChars
 * @package Light\Filter
 */
class HtmlSpecialChars extends FilterAbstract
{
    /**
     * @param $value
     * @return mixed|string
     */
    public function filter($value)
    {
        return htmlspecialchars($value);
    }
}