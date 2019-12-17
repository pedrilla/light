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
     * @param array $requestedRoute
     * @param array $params
     * @return string
     *
     * @throws DomainMustBeProvided
     * @throws RouterVarMustBeProvided
     */
    public function assemble(array $requestedRoute = [], array $params = [])
    {
        $module = $requestedRoute['module'] ?? $this->_module;
        $controller = $requestedRoute['controller'] ?? $this->_controller;
        $action = $requestedRoute['action'] ?? $this->_action;

        $uri = null;
        $selectedDomain = null;

        foreach ($this->_routes as $domain => $router) {

            if ($router['module'] == $module) {

                $selectedDomain = $domain;

                foreach ($router['routes'] ?? [] as $routeUri => $route) {

                    $currentRouteSettings = [
                        'controller' => $route['controller'] ?? 'index',
                        'action' => $route['action'] ?? 'index'
                    ];

                    if ($controller == $currentRouteSettings['controller']
                        && $action == $currentRouteSettings['action']) {

                        $releaseParts = [];

                        foreach (explode('/', $routeUri) as $part) {

                            if (substr($part, 0, 1) == ':') {

                                $var = substr($part, 1);

                                if (!isset($params[$var])) {
                                    throw new RouterVarMustBeProvided($var);
                                }

                                $releaseParts[] = $params[$var];
                                unset($params[$var]);
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
            $uri = '/' . implode('/', [$controller, $action]);
        }

        if (count($params)) {

            $get = [];

            foreach ($params as $key => $value) {
                $get[] = "$key=$value";
            }

            $uri = $uri . '?' . implode('&', $get);
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

        if (isset($routes['routes'][$uri])) {

            $this->_controller = $routes['routes'][$uri]['controller'] ?? 'index';
            $this->_action = $routes['routes'][$uri]['action'] ?? 'index';

            foreach ($routes['routes'][$uri]['params'] ?? [] as $key => $value) {
                $this->getRequest()->setGetParam($key, $value);
                $this->_urlParams[$key] = $value;
            }

            return;
        }

        foreach ($routes['routes'] as $routerUri => $settings) {

            $pattern = preg_replace('/\\\:[А-Яа-яЁёa-zA-Z0-9\_\-]+/', '([А-Яа-яЁёa-zA-Z0-9\-\_]+)', preg_quote($routerUri, '@'));
            $pattern = "@^$pattern/?$@uD";

            $matches = [];

            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                $this->_controller = $settings['controller'] ?? 'index';
                $this->_action = $settings['action'] ?? 'index';

                $paramIndex = 0;

                foreach (explode('/', $routerUri) as $routerUriPart) {

                    if (substr($routerUriPart, 0, 1) == ':') {

                        $this->getRequest()->setGetParam(
                            substr($routerUriPart, 1),
                            $matches[$paramIndex]
                        );

                        $this->_urlParams[substr($routerUriPart, 1)] = $matches[$paramIndex];

                        $paramIndex++;
                    }
                }

                $this->_injector = $settings['injector'] ?? [];

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