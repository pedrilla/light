<?php

declare(strict_types = 1);

namespace Light;

use Light\Exception\DomainMustBeProvided;
use Light\Exception\RouterDomainWasNotFound;
use Light\Exception\RouterVarMustBeProvided;
use Light\Exception\RouterWasNotFound;

/**
 * Class Router
 * @package Light
 */
class Router
{
    /**
     * @var Request
     */
    private $_request = null;

    /**
     * @var string
     */
    private $_module = '';

    /**
     * @var string
     */
    private $_controller = '';

    /**
     * @var string
     */
    private $_action = '';

    /**
     * @var array
     */
    private $_routes = [];

    /**
     * @var array
     */
    private $_urlParams = [];

    /**
     * @var array
     */
    private $_injector = [];

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->_request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->_module;
    }

    /**
     * @param string $module
     */
    public function setModule(string $module)
    {
        $this->_module = $module;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->_controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->_action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->_action = $action;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;
    }

    /**
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->_urlParams;
    }

    /**
     * @param array $urlParams
     */
    public function setUrlParams(array $urlParams): void
    {
        $this->_urlParams = $urlParams;
    }

    /**
     * @return array
     */
    public function getInjector(): array
    {
        return $this->_injector;
    }

    /**
     * @param array $injector
     */
    public function setInjector(array $injector): void
    {
        $this->_injector = $injector;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->_config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->_config = $config;
    }

    /**
     * @param array $requestedRoute
     * @param array $params
     * @param bool $reset
     *
     * @return string
     *
     * @throws DomainMustBeProvided
     * @throws RouterVarMustBeProvided
     */
    public function assemble(array $requestedRoute = [], array $params = [], bool $reset = false)
    {
        $module = $requestedRoute['module'] ?? $this->_module;
        $controller = $requestedRoute['controller'] ?? ($reset ? '' : $this->_controller);
        $action = $requestedRoute['action'] ?? ($reset ? '' : $this->_action);

        if (!$reset) {
            $params = array_merge($this->_urlParams, $params);
        }

        $uri = null;
        $selectedDomain = null;

        foreach ($this->_routes as $domain => $router) {

            if (($router['module'] ?? null) == $module) {

                $selectedDomain = $domain;

                foreach ($router['routes'] ?? [] as $routeUri => $route) {

                    if ($routeUri == '*' && is_callable($route)) {
                        continue;
                    }

                    $routeUri = ($router['prefix'] ?? '') . $routeUri;

                    $currentRouteSettings = [
                        'controller' => $route['controller'] ?? 'index',
                        'action' => $route['action'] ?? 'index'
                    ];

                    $chController = $controller == '' ? 'index' : $controller;
                    $chAction = $action == '' ? 'index' : $action;

                    if ($chController == $currentRouteSettings['controller']
                        && $chAction == $currentRouteSettings['action']) {

                        $releaseParts = [];

                        foreach (explode('/', $routeUri) as $part) {

                            if (substr($part, 0, 1) == ':') {

                                $var = substr($part, 1);

                                $val = $params[$var] ?? null;

                                if ($params[$var] ?? null) {
                                    $releaseParts[] = $params[$var];
                                    unset($params[$var]);
                                }
                            }
                            else {
                                $releaseParts[] = $part;
                            }
                        }

                        $uri = implode('/', $releaseParts);
                    }
                }
            }
        }

        if (!$selectedDomain) {
            throw new DomainMustBeProvided();
        }

        if (!$uri) {
            $uri = '/' . implode('/', array_filter([$controller, $action]));
        }

        if (count($params)) {
            $uri = $uri . '?' . http_build_query($params);
        }

        if ($selectedDomain == '*') {
            $selectedDomain = $this->getRequest()->getDomain();
        }

        $port = '';

        if ($this->getRequest()->getPort() != 80 && $this->getRequest()->getPort() != 443) {
            $port = ':' . $this->getRequest()->getPort();
        }

        return $this->getRequest()->getScheme() . '://' . $selectedDomain . $port . $uri;
    }

    /**
     * @throws RouterDomainWasNotFound
     * @throws RouterWasNotFound
     */
    public function parse()
    {
        $domain = $this->getRequest()->getDomain();

        if (!isset($this->_routes[$domain]) && !isset($this->_routes['*']) && count($this->_routes)) {
            throw new RouterDomainWasNotFound($domain);
        }
        else if (!isset($this->_routes[$domain])) {
            $domain = '*';
        }

        $this->setConfig($this->_routes[$domain]['light'] ?? []);
        
        $routes = $this->_routes[$domain] ?? [];
        $this->_module = $routes['module'] ?? '';

        $uri = explode('?', $this->getRequest()->getUri())[0];

        $parts = array_values(array_filter(explode('/', $uri)));

        if (!isset($routes['routes'])) {

            if (isset($routes['strict']) && $routes['strict'] === true) {
                throw new RouterWasNotFound($uri);
            }

            $this->_controller = $parts[0] ?? 'index';
            $this->_action = $parts[1] ?? 'index';

            return;
        }

        $prefix = $routes['prefix'] ?? '';

        if ($uri != '/' && substr($uri, -1) == '/') {
            $uri = $uri . '/';
        }

        $match = false;
        $withPrefix = false;

        foreach ($routes['routes'] as $routerUri => $settings) {

            if (substr($uri, -2) == '//' && $routerUri != '/') {
                 $routerUri = $routerUri . '/';
            }

            $pattern = preg_replace('/\\\:[А-Яа-яЁёa-zA-Z0-9\_\-]+/', '([А-Яа-яЁёa-zA-Z0-9\-\_]+)', preg_quote($routerUri, '@'));
            $pattern = "@^$pattern/?$@uD";

            $matches = [];

            if (preg_match($pattern, $uri, $matches)) {
                $match = true;
                break;
            }
        }

        if (!$match) {

            $withPrefix = true;

            foreach ($routes['routes'] as $routerUri => $settings) {

                if (substr($uri, -2) == '//' && $routerUri != '/') {
                    $routerUri = $routerUri . '/';
                }

                $patternPrefix = preg_replace('/\\\:[А-Яа-яЁёa-zA-Z0-9\_\-]+/', '([А-Яа-яЁёa-zA-Z0-9\-\_]+)', preg_quote($prefix . $routerUri, '@'));
                $patternPrefix = "@^$patternPrefix/?$@uD";

                if (preg_match($patternPrefix, $uri, $matches)) {
                    $match = true;
                    break;
                }
            }
        }

        if ($match) {

            array_shift($matches);

            $this->_controller = $settings['controller'] ?? 'index';
            $this->_action = $settings['action'] ?? 'index';
            $this->_urlParams = $settings['params'] ?? [];
            
            $paramIndex = 0;

            if ($withPrefix) {
                $routerUri = $prefix . $routerUri;
            }

            foreach (explode('/', $routerUri) as $routerUriPart) {

                if (substr($routerUriPart, 0, 1) == ':') {

                    $this->getRequest()->setGetParam(
                        substr($routerUriPart, 1),
                        $matches[$paramIndex] ?? null
                    );

                    $this->_urlParams[substr($routerUriPart, 1)] = $matches[$paramIndex] ?? null;

                    $paramIndex++;
                }
            }

            $this->_injector = array_merge($settings['injector'] ?? [], $routes['prefixInjector'] ?? []);
            
            foreach ($this->_urlParams as $key => $value) {
                $this->getRequest()->setGetParam($key, $value);
            }

            return;
        }

        if (isset($routes['routes']['*']) && is_callable($routes['routes']['*'])) {

            $route = $routes['routes']['*']($uri);

            if (is_array($route)) {

                $this->_controller = $route['controller'] ?? 'index';
                $this->_action = $route['action'] ?? 'index';
                $this->_urlParams = $route['params'] ?? [];
                $this->_injector = $route['injector'] ?? [];

                foreach ($this->_urlParams as $key => $value) {
                    $this->getRequest()->setGetParam($key, $value);
                }

                return;
            }
        }

        if (isset($routes['strict']) && $routes['strict'] === true) {
            throw new RouterWasNotFound($uri);
        }

        $this->_controller = $parts[0] ?? 'index';
        $this->_action = $parts[1] ?? 'index';

        $parts = array_slice($parts, 2);

        for ($i = 0; $i < count($parts); $i += 2) {

            if (isset($parts[$i + 1])) {
                $this->getRequest()->setGetParam($parts[$i], $parts[$i + 1]);
                $this->_urlParams[$parts[$i]] = $parts[$i + 1];
            }
        }
    }
}
