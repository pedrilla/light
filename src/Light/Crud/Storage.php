<?php

declare(strict_types = 1);

namespace Light\Crud;

use Light\Controller;
use Light\Front;

/**
 * Class Storage
 * @package Light\Crud
 */
class Storage extends Controller
{
    /**
     * @return false|string
     */
    public function getStorageContent()
    {
        return file_get_contents(
            Front::getInstance()->getConfig()['light']['storage']['url']
        );
    }

    /**
     * @return string
     */
    public function index()
    {
        $this->getView()->setLayoutEnabled(false);

        return $this->getStorageContent();
    }
}