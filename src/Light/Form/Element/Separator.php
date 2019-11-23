<?php

declare(strict_types = 1);

namespace Light\Form\Element;

use Light\View;

/**
 * Class Separator
 * @package Light\Form\Element
 */
class Separator extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/separator';

    /**
     * Separator constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = [])
    {
        parent::__construct($name, $options);

        $this->setLabel($name);
    }

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