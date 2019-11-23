<?php

declare(strict_types = 1);

namespace Light;

use Light\Form\Element\ElementAbstract;
use Light\Form\Element\Separator;
use Light\View;

/**
 * Class Form
 * @package Light\Form
 */
class Form
{
    /**
     * @var ElementAbstract
     */
    public $elements = [];

    /**
     * @var bool
     */
    public $readOnly = false;

    /**
     * @var string
     */
    public $submit = 'Save';

    /**
     * @var string
     */
    public $template = 'index.phtml';

    /**
     * @var View
     */
    public $view = null;

    /**
     * @var string
     */
    public $method = 'POST';

    /**
     * @var string
     */
    public $action = null;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var string
     */
    public $_returnUrl = null;

    /**
     * @return ElementAbstract[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param ElementAbstract[] $elements
     */
    public function setElements(array $elements): void
    {
        if (count($elements)) {

            $firstKey = array_keys($elements)[0];

            if (is_string($firstKey)) {

                foreach ($elements as $separator => $groupElements) {

                    $this->addElement(new Separator($separator));

                    foreach ($groupElements as $element) {
                        $this->elements[$element->getName()] = $element;
                    }
                }
            }
            else {
                foreach ($elements as $element) {
                    $this->elements[$element->getName()] = $element;
                }
            }
        }
    }

    /**
     * @param ElementAbstract $element
     */
    public function addElement(ElementAbstract $element)
    {
        $this->elements[$element->getName()] = $element;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @return array
     */
    public function getValues() : array
    {
        $values = [];

        foreach ($this->getElements() as $name => $element) {
            $values[$name] = $element->getValue();
        }

        return $values;
    }

    /**
     * @return string
     */
    public function getSubmit()
    {
        return $this->submit;
    }

    /**
     * @param string $submit
     */
    public function setSubmit(string $submit): void
    {
        $this->submit = $submit;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return array|Model[]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|Model $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->_returnUrl;
    }

    /**
     * @param string $returnUrl
     */
    public function setReturnUrl(string $returnUrl): void
    {
        $this->_returnUrl = $returnUrl;
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws Exception\ValidatorClassWasNotFound
     */
    public function isValid(array $data = []) : bool
    {
        $isValid = true;

        foreach ($this->getElements() as $name => $element) {

            if (!$element->isValid($data[$name] ?? null)) {

                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * Form constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $value) {

            if (is_callable([$this, 'set' . ucfirst($name)])) {
                call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
            }
        }

        $this->init($this->data);

        foreach ($this->getElements() as $element) {
            $element->init();
        }
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function __toString() : string
    {
        if (!$this->view) {
            $this->view = new View();
            $this->view->setPath(realpath(__DIR__ . '/Form'));
            $this->view->setScript('index');
        }

        $this->view->assign('form', $this);

        return $this->view->render();
    }

    /**
     * @param Model|null $model
     */
    public function init($model = null) {}
}