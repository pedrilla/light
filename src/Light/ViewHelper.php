<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class ViewHelper
 * @package Light
 */
abstract class ViewHelper
{
    /**
     * @var View
     */
    protected $_view = null;

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->_view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->_view = $view;
    }
}