<?php

declare(strict_types = 1);

namespace Light\Form\Element;

use Light\View;

/**
 * Class Hidden
 * @package Light\Form\Element
 */
class Hidden extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/hidden';

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString(): string
    {
        if (!$this->view) {
            $this->view = new View();
            $this->view->setPath(realpath(__DIR__ . '/../'));
            $this->view->setScript($this->getElementTemplate());
        }

        $this->view->assign('element', $this);

        return $this->view->render();
    }
}