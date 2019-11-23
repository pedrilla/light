<?php

declare(strict_types = 1);

namespace Light\Form\Element;

use Light\Filter\FilterAbstract;
use Light\Validator\ValidatorAbstract;
use Light\View;

abstract class ElementAbstract
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string|int|object|null|bool
     */
    public $value = null;

    /**
     * @var string
     */
    public $label = null;

    /**
     * @var string
     */
    public $description = null;

    /**
     * @var string
     */
    public $hint = null;

    /**
     * @var FilterAbstract[]
     */
    public $filters = [];

    /**
     * @var ValidatorAbstract[]
     */
    public $validators = [];

    /**
     * @var bool
     */
    public $allowNull = true;

    /**
     * @var bool
     */
    public $readOnly = false;

    /**
     * @var string[]
     */
    public $errorMessages = [];

    /**
     * @var string
     */
    public $containerTemplate = 'element/container';

    /**
     * @var string
     */
    public $errorTemplate = 'element/error';

    /**
     * @var string
     */
    public $labelTemplate = 'element/label';

    /**
     * @var string
     */
    public $elementTemplate = null;

    /**
     * @var View
     */
    public $view = null;

    /**
     * @var string
     */
    public $placeholder = null;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool|int|object|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool|int|object|string|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint ?? 'Select item';
    }

    /**
     * @param string $hint
     */
    public function setHint(string $hint): void
    {
        $this->hint = $hint;
    }

    /**
     * @return FilterAbstract[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param FilterAbstract[] $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return ValidatorAbstract[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param ValidatorAbstract[] $validators
     */
    public function setValidators(array $validators): void
    {
        $this->validators = $validators;
    }

    /**
     * @param array $validator
     */
    public function addValidator(array $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->allowNull;
    }

    /**
     * @param bool $allowNull
     */
    public function setAllowNull(bool $allowNull): void
    {
        $this->allowNull = $allowNull;
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
     * @return bool
     */
    public function hasError() : bool
    {
        return (bool)count($this->getErrorMessages());
    }

    /**
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param string[] $errorMessages
     */
    public function setErrorMessages(array $errorMessages): void
    {
        $this->errorMessages = $errorMessages;
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
     * ElementAbstract constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = [])
    {
        $this->setName($name);

        foreach ($options as $name => $value) {

            if (is_callable([$this, 'set' . ucfirst($name)])) {
                call_user_func_array([$this, 'set' . ucfirst($name)], [$value]);
            }
        }
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        $template = explode('\\', get_called_class());
        return strtolower($template[count($template)-1]);
    }

    /**
     * @param $value
     * @return bool
     * @throws \Light\Exception\ValidatorClassWasNotFound
     */
    public function isValid($value) : bool
    {
        $this->errorMessages = [];

        foreach ($this->getValidators() as $validatorClassName => $settings) {

            if (!class_exists($validatorClassName)) {
                throw new \Light\Exception\ValidatorClassWasNotFound($validatorClassName);
            }

            /** @var ValidatorAbstract $validator */
            $validator = new $validatorClassName($settings['options'] ?? []);

            $validator->setAllowNull($this->isAllowNull());

            if (!$validator->isValid($value)) {
                $this->errorMessages[] = $settings['message'] ?? '';
            }
        }

        $this->value = $value;

        if (!count($this->errorMessages)) {

            foreach ($this->getFilters() as $filterClassName => $settings) {

                if (is_numeric($filterClassName)) {
                    $filterClassName = $settings;
                }

                /** @var FilterAbstract $filter */
                $filter = new $filterClassName($settings['options'] ?? []);

                $value = $filter->filter($value);
            }

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getContainerTemplate(): string
    {
        return $this->containerTemplate;
    }

    /**
     * @param string $containerTemplate
     */
    public function setContainerTemplate(string $containerTemplate): void
    {
        $this->containerTemplate = $containerTemplate;
    }

    /**
     * @return string
     */
    public function getErrorTemplate(): string
    {
        return $this->errorTemplate;
    }

    /**
     * @param string $errorTemplate
     */
    public function setErrorTemplate(string $errorTemplate): void
    {
        $this->errorTemplate = $errorTemplate;
    }

    /**
     * @return string
     */
    public function getLabelTemplate(): string
    {
        return $this->labelTemplate;
    }

    /**
     * @param string $labelTemplate
     */
    public function setLabelTemplate(string $labelTemplate): void
    {
        $this->labelTemplate = $labelTemplate;
    }

    /**
     * @return string
     */
    public function getElementTemplate(): string
    {
        return $this->elementTemplate;
    }

    /**
     * @param string $elementTemplate
     */
    public function setElementTemplate(string $elementTemplate): void
    {
        $this->elementTemplate = $elementTemplate;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString() : string
    {
        if (!$this->view) {
            $this->view = new View();
            $this->view->setPath(realpath(__DIR__ . '/../'));
            $this->view->setScript($this->getContainerTemplate());
        }

        $this->view->assign('element', $this);

        return $this->view->render();
    }

    public function init() {}
}