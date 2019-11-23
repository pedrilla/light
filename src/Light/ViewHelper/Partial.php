<?php

namespace Light\ViewHelper;

use Light\ViewHelper;

/**
 * Class Partial
 * @package Light\ViewHelper
 */
class Partial extends ViewHelper
{
    /**
     * @param $template
     * @param array $vars
     *
     * @return false|string
     * @throws \Exception
     */
    public function call($template, array $vars = [])
    {
        return $this->getView()->render($template, $vars);
    }
}