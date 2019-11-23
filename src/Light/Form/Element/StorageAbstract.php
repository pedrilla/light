<?php

declare(strict_types = 1);

namespace Light\Form\Element;

use Light\Front;

abstract class StorageAbstract extends ElementAbstract
{
    /**
     * @var string
     */
    public $storageUrl = null;

    /**
     * @return string
     */
    public function getStorageUrl(): string
    {
        return $this->storageUrl;
    }

    /**
     * @param string $storageUrl
     */
    public function setStorageUrl(string $storageUrl): void
    {
        $this->storageUrl = $storageUrl;
    }

    public function init()
    {
        parent::init();

        $this->setStorageUrl(
            Front::getInstance()->getConfig()['light']['storage']['url']
        );
    }
}