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
     * @var bool
     */
    protected $_minify = false;

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
                'Helper',
                ucfirst($name)
            ]);
        }
        else {
            $helperClassName = implode('\\', [
                Front::getInstance()->getConfig()['light']['loader']['namespace'],
                'View',
                'Helper',
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
     * @return bool
     */
    public function isMinify(): bool
    {
        return $this->_minify;
    }

    /**
     * @param bool $minify
     */
    public function setMinify(bool $minify): void
    {
        $this->_minify = $minify;
    }

    /**
     * @param string|null $template
     * @param array $vars
     * @return string
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

        try {
            $content = $this->_render($this->_path . '/Scripts/' . ($template ?? $this->_script) . '.phtml');
        }
        catch (\Exception $e) {

            $config = Front::getInstance()->getConfig();

            if ($config['light']['modules'] ?? false) {

                $modules = implode('/', array_slice(explode('\\', $config['light']['modules']), 2));

                $viewPath = realpath(implode('/', [
                    $config['light']['loader']['path'],
                    $modules,
                    ucfirst(Front::getInstance()->getRouter()->getModule()),
                    'View'
                ]));
            } else {

                $viewPath = realpath(implode('/', [
                    $config['light']['loader']['path'],
                    'View'
                ]));
            }

            try {
                $content = $this->_render($viewPath . '/' . ($template ?? $this->_script) . '.phtml');
            }
            catch (\Exception $_e) {
                throw $e;
            }
        }

        return $content;
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

        if ($this->isMinify()) {

            $search = array(
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            );

            $replace = array(
                '>',
                '<',
                '\\1',
                ''
            );

            $content = preg_replace($search, $replace, $content);
        }

        return $content;
    }
}
