<?php

declare(strict_types = 1);

namespace Light;

use Light\Crud\Storage;
use Light\Exception\ActionMethodIsReserved;
use Light\Model\Meta\Exception\PropertyIsSetIncorrectly;
use Light\Model\Meta\Property;

/**
 * Class Front
 * @package Light
 */
final class Front
{
    /**
     * @var Front
     */
    private static $_instance = null;

    /**
     * @param array $config
     * @return Front
     */
    public static function getInstance(array $config = [])
    {
        if (!self::$_instance) {
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @var Loader
     */
    private $_loader = null;

    /**
     * @var BootstrapAbstract
     */
    private $_bootstrap = null;

    /**
     * @var Request
     */
    private $_request = null;

    /**
     * @var Response
     */
    private $_response = null;

    /**
     * @var Router
     */
    private $_router = null;

    /**
     * @var View
     */
    private $_view = null;

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->_router;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->_router = $router;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->_response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->_response = $response;
    }

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
     * @return BootstrapAbstract
     */
    public function getBootstrap(): BootstrapAbstract
    {
        return $this->_bootstrap;
    }

    /**
     * @param BootstrapAbstract $bootstrap
     */
    public function setBootstrap(BootstrapAbstract $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

    /**
     * @return Loader
     */
    public function getLoader(): Loader
    {
        return $this->_loader;
    }

    /**
     * @param Loader $loader
     */
    public function setLoader(Loader $loader)
    {
        $this->_loader = $loader;
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
     * @return View
     */
    public function getView(): View
    {
        return $this->_view;
    }

    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->_view = $view;
    }

    /**
     * Front constructor.
     * @param array $config
     */
    private function __construct(array $config = [])
    {
        $this->_config = $config;
        $this->_loader = new Loader($this->_config);

        $bootstrapClassName = '\\' . $this->_config['light']['loader']['namespace'] . '\\Bootstrap';

        if (class_exists($bootstrapClassName)) {
            $this->_bootstrap = new $bootstrapClassName();
            $this->_bootstrap->setConfig($this->_config);
        }

        if (isset($this->_config['light']['phpIni'])) {

            foreach ($this->_config['light']['phpIni'] as $key => $val) {
                ini_set($key, $val);
            }
        }

        if (isset($this->_config['light']['startup'])) {

            foreach ($this->_config['light']['startup'] as $key => $val) {

                if (function_exists($key)) {

                    if (!is_array($val)) {
                        $val = [$val];
                    }

                    call_user_func_array($key, $val);
                }
            }
        }
    }

    /**
     * @return $this
     * @throws \ReflectionException
     */
    public function bootstrap()
    {
        if ($this->_bootstrap) {

            $bootReflection = new \ReflectionClass(
                $this->_bootstrap
            );

            foreach ($bootReflection->getMethods() as $method) {

                if ($method->class != 'Light\\BootstrapAbstract') {
                    $this->_bootstrap->{$method->name}();
                }
            }
        }

        set_error_handler(function($number, $message, $file, $line) {
            throw new \Exception(implode(':', [$message, $file, $line]), $number);
        });

        return $this;
    }

    /**
     * @param \Exception|null $exception
     * @return string
     *
     * @throws \Exception
     */
    public function run(\Exception $exception = null)
    {
        if (!$this->_request) {

            $this->_request = new Request();

            if (php_sapi_name() !== 'cli') {
                $this->_request->fillRequestFromServer();
            }
            else {
                $this->_request->fillRequestFromCli();
            }
        }

        if (!$this->_response) {
            $this->_response = new Response();
        }

        try {

            if (!$this->_router) {
                $this->_router = new Router();
                $this->_router->setRoutes($this->_config['router'] ?? []);
                $this->_router->setRequest($this->_request);
                $this->_router->parse();
            }

            $this->_view = new View();

            if ($this->_config['light']['modules'] ?? false) {

                $modules = implode('/', array_slice(explode('\\', $this->_config['light']['modules']), 2));

                $viewPath = realpath(implode('/', [
                    $this->_config['light']['loader']['path'],
                    $modules,
                    ucfirst($this->_router->getModule()),
                    'View'
                ]));
            }
            else {

                $viewPath = realpath(implode('/', [
                    $this->_config['light']['loader']['path'],
                    'View'
                ]));
            }

            if ($viewPath) {

                $this->_view->setPath($viewPath);

                $this->_view->setLayoutEnabled(true);
                $this->_view->setLayoutTemplate('index');
                $this->_view->setScript($this->_router->getController() . '/' . $this->_router->getAction());
            }
            else {
                $this->_view->setAutoRender(false);
                $this->_view->setLayoutEnabled(false);
            }

            $controllerClassName = $this->getControllerClassName($this->_router);

            if (!class_exists($controllerClassName, true) || !is_subclass_of($controllerClassName, '\\Light\\Controller')) {

                if ($exception) {
                    throw $exception;
                }

                throw new Exception\ControllerClassWasNotFound($controllerClassName);
            }

            /** @var Controller|ErrorController $controller */
            $controller = new $controllerClassName();

            $controller->setRequest($this->_request);
            $controller->setResponse($this->_response);
            $controller->setRouter($this->_router);
            $controller->setView($this->_view);

            if ($exception && is_subclass_of($controller, '\\Light\\ErrorController')) {
                $controller->setException($exception);
                $controller->setExceptionEnabled($this->_config['light']['exception'] ?? false);
            }
            else if ($exception) {
                throw $exception;
            }

            /** @var Plugin[] $plugins */

            $plugins = [];

            $pluginsPath = realpath($this->_config['light']['loader']['path'] . '/Plugin');

            if ($pluginsPath) {

                foreach (glob($pluginsPath . '/*.php') as $pluginClass) {

                    $pluginClassName = '\\' . $this->_config['light']['loader']['namespace'] . '\\Plugin\\' . str_replace('.php', null, basename($pluginClass));

                    if (is_subclass_of($pluginClassName, '\\Light\\Plugin')) {
                        $plugins[] = new $pluginClassName();
                    }
                }
            }

            foreach ($plugins as $plugin) {
                $plugin->preRun($this->_request, $this->_router);
            }

            $controller->init();

            if (is_callable([$controller, $this->_router->getAction()])) {

                if (in_array($this->_router->getAction(), get_class_methods(Controller::class))) {
                    throw new ActionMethodIsReserved($this->_router->getAction());
                }

                $content = call_user_func_array(
                    [$controller, $this->_router->getAction()],
                    $this->inject($controller, $this->_router)
                );

                if (is_null($content) && $this->_view->isAutoRender()) {
                    $content = $this->_view->render();
                }

                if (is_object($content) && $content instanceof Map) {
                    /** @var Map $content */
                    $content = $content->toArray();
                }
                
                if (is_array($content)) {
                    $content = json_encode($content, JSON_PRETTY_PRINT);
                    $this->_response->setHeader('Content-type', 'application/json');
                }
                else if ($this->_view->isLayoutEnabled()) {
                    $this->_view->setContent($content ?? '');
                    $content = $this->_view->renderLayout();
                }

                $this->_response->setBody($content);
            }
            else {
                throw new Exception\ActionMethodWasNotFound($this->_router->getAction());
            }

            $controller->postRun();

            foreach ($plugins as $plugin) {
                $plugin->postRun($this->_request, $this->_response, $this->_router);
            }

            return $this->render($this->_response);
        }
        catch (\Exception $localException) {

            if (!$exception) {

                $errorRouter = new Router();

                $errorRouter->setRequest($this->_request);
                $errorRouter->setModule($this->_router->getModule());
                $errorRouter->setController('error');
                $errorRouter->setAction('index');

                $this->setRouter($errorRouter);
                return $this->run($localException);
            }

            if ($this->_config['light']['exception'] ?? false) {
                throw $exception;
            }
        }
    }

    /**
     * @param Controller $controller
     * @param Router $router
     *
     * @return array
     * @throws \ReflectionException
     */
    public function inject(Controller $controller, Router $router)
    {
        $reflection = new \ReflectionMethod($controller, $router->getAction());
        $injector = $router->getInjector();

        $docComment = str_replace('*', '', $reflection->getDocComment());

        $docComment = array_filter(array_map(function($line) {

            $line = trim($line);

            if (strlen($line) > 0) {
                return $line;
            }
        }, explode("\n", $docComment)));

        $params = [];

        foreach ($docComment as $line) {

            if (substr($line, 0, strlen('@param')) == '@param') {

                $param = explode('|', str_replace('$', null, array_values(array_filter(explode(' ', $line)))[2]));

                $params[$param[0]] = $param[1] ?? 'id';
            }
        }

        $args = [];

        foreach ($reflection->getParameters() as $parameter) {

            $var = $parameter->getName();

            if (isset($injector[$var])) {
                $args[$var] = $injector[$var]($router->getUrlParams()[$var]);
            }
            else {

                $value = $router->getUrlParams()[$var];

                if (!$parameter->getType()) {
                    $args[$var] = $value;
                    continue;
                }

                switch ($parameter->getType()->getName()) {

                    case 'int':
                        $args[$var] = intval($value);
                        break;

                    case 'string':
                        $args[$var] = strval($value);
                        break;

                    case 'bool':
                        $args[$var] = boolval($value);
                        break;

                    default:

                        $className = $parameter->getType()->getName();

                        try {

                            if (is_subclass_of($parameter->getType()->getName(), '\\Light\\Model')) {

                                if (isset($params[$parameter->getName()])) {

                                    /** @var Model $model */
                                    $model = new $className();

                                    $property = $model->getMeta()->getPropertyWithName(
                                        $params[$parameter->getName()]
                                    );

                                    try {
                                        settype($value, $property->getType());
                                    }
                                    catch (\Exception $exception) {
                                        $value = (string)$value;
                                    }

                                    if ($parameter->allowsNull()) {
                                        /** @var Model $className */
                                        $args[$var] = $className::fetchObject([
                                            $params[$parameter->getName()] => $value
                                        ]);
                                    }
                                    else {
                                        /** @var Model $className */
                                        $args[$var] = $className::fetchOne([
                                            $params[$parameter->getName()] => $value
                                        ]);
                                    }
                                }
                            }

                            else {
                                $args[$var] = new $className($value);
                            }
                        }
                        catch (\Exception $e) {
                            $args[$var] = $value;
                        }
                }
            }
        }

        return $args;
    }

    /**
     * @param Router $router
     * @return string
     */
    public function getControllerClassName(Router $router) : string
    {
        $module = $router->getModule();
        $controller = $router->getController();

        if (($this->getConfig()['light']['storage']['route'] ?? false) == $controller) {
            return Storage::class;
        }

        if ($this->_config['light']['modules'] ?? false) {
            return implode('\\', [
                $this->_config['light']['modules'],
                ucfirst($module),
                'Controller',
                ucfirst($controller),
            ]);
        }

        return implode('\\', [
            $this->_config['light']['loader']['namespace'],
            'Controller',
            ucfirst($controller),
        ]);
    }

    /**
     * @param Response $response
     * @return string
     */
    public function render(Response $response)
    {
        /** Setup Status **/
        $statusCode = $response->getStatusCode();

        $phpSapiName = substr(php_sapi_name(), 0, 3);

        if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
            header('Status: ' . $statusCode);
        }
        else {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($protocol . ' ' . $statusCode);
        }

        /** Setup Headers **/
        foreach ($response->getHeaders() as $name => $value) {
            header($name . ': ' . $value);
        }

        return $response->getBody();
    }
}
