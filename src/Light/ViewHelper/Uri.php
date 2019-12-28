<?php

namespace Light\ViewHelper;

use Light\Front;
use Light\ViewHelper;

/**
 * Class Uri
 * @package Light\ViewHelper
 */
class Uri extends ViewHelper
{
    /**
     * @param array $route
     * @param array $params
     * @param bool $reset
     *
     * @return string
     *
     * @throws \Light\Exception\DomainMustBeProvided
     * @throws \Light\Exception\RouterVarMustBeProvided
     */
    public function call(array $route = [], array $params = [], bool $reset = false)
    {
        return Front::getInstance()->getRouter()->assemble($route, $params, $reset);
    }
}
