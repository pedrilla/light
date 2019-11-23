<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class View
 * @package Light
 */
class View
{
    /**
     * @var string
     */
    protected $_path = null;

    /**
     * @var bool
     */
    protected $_layoutEnabled = true;

    /**
     * @var string
     */
    protected $_layoutTemplate = null;

    /**
     * @var string
     */
    protected $_script = null;

    /**
     * @var bool
     */
    protected $_autoRender = true;

    /**
     * @var string
     */
    protected $_content = null;

    /**
     * @var array
     */
    protected $_vars = [];

    /**
     * @param string $key
     * @param $value
     */
    public function assign(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * @param array $vars
     */
    public function setVars(array $vars)
    {
        $this->_vars = $vars;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->_path = $path;
    }

    /**
     * @return bool
     */
    public function isLayoutEnabled(): bool
    {
        return $this->_layoutEnabled;
    }

    /**
     * @param bool $layoutEnabled
     */
    public function setLayoutEnabled(bool $layoutEnabled): void
    {
        $this->_layoutEnabled = $layoutEnabled;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->_vars[$name] ?? null;
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value)
    {
        $this->_vars[$name] = $value;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (Front::getInstance()->getConfig()['light']['modules'] ?? false) {

            $helperClassName = implode('\\', [
                Front::getInstance()->getConfig()['light']['modules'],
                ucfirst(Front::getInstance()->getRouter()->getModule()),
                'View',
                'Helpers',
                ucfirst($name)
            ]);
        }
        else {
            $helperClassName = implode('\\', [
                Front::getInstance()->getConfig()['light']['loader']['namespace'],
                'View',
                'Helpers',
                ucfirst($name)
            ]);
        }

        if (!class_exists($helperClassName)) {
            $helperClassName = implode('\\', ['Light', 'ViewHelper', ucfirst($name)]);
        }

        /** @var ViewHelper $helper */
        $helper = new $helperClassName();
        $helper->setView($this);

        return call_user_func_array([$helper, 'call'], $arguments);
    }

    /**
     * @return string
     */
    public function getLayoutTemplate(): string
    {
        return $this->_layoutTemplate;
    }

    /**
     * @param string $layoutTemplate
     */
    public function setLayoutTemplate(string $layoutTemplate): void
    {
        $this->_layoutTemplate = $layoutTemplate;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->_script;
    }

    /**
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->_script = $script;
    }

    /**
     * @return bool
     */
    public function isAutoRender(): bool
    {
        return $this->_autoRender;
    }

    /**
     * @param bool $autoRender
     */
    public function setAutoRender(bool $autoRender): void
    {
        $this->_autoRender = $autoRender;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->_content = $content;
    }

    /**
     * @param string|null $template
     * @return false|string
     * @throws \Exception
     */
    public function render(string $template = null, array $vars = [])
    {
        if (count($vars)) {

            $view = new self();

            $view->setPath($this->_path);
            $view->setVars($vars);

            return $view->render($template);
        }

        return $this->_render($this->_path . '/Scripts/' . ($template ?? $this->_script) . '.phtml');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function renderLayout()
    {
        return $this->_render($this->_path . '/Layouts/' . $this->_layoutTemplate . '.phtml');
    }

    /**
     * @param string $template
     * @return string
     * @throws \Exception
     */
    private function _render(string $template) : string
    {
        $exception = null;

        ob_start();

        try {
            include $template;
        }
        catch (\Exception $e) {
            $exception = $e;
        }

        $content = ob_get_contents();

        ob_end_clean();

        if ($exception) {
            throw $exception;
        }

        return $content;
    }
}