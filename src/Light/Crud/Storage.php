<?php

declare(strict_types=1);

namespace Light\Crud;

use Light\Controller;
use Light\Crud;
use Light\Front;

/**
 * Class Storage
 * @package Light\Crud
 */
class Storage extends Crud
{
    public function init()
    {
        parent::init();

        if ($this->getRequest()->isAjax()) {
            $this->getView()->setLayoutEnabled(false);
        }
    }

    /**
     * @return string
     */
    public function index()
    {
        $this->getView()->setScript('storage');
    }

    /**
     * @return false|string
     */
    public function frame()
    {
        $storageConfig = Front::getInstance()->getConfig()['light']['storage'];
        
        return file_get_contents(
            $storageConfig['url'] . '?key=' . $storageConfig['key']
        );
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function modal()
    {
        return $this->getView()->render('storage');
    }
}
