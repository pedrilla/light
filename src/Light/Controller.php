<?php

declare(strict_types = 1);

namespace Light;

/**
 * Class Controller
 * @package Light
 */
class Controller
{
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
     * @return Response
     */
    public function getResponse()
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
     * @return Router
     */
    public function getRouter()
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
     * @param string $uri
     */
    public function redirect(string $uri)
    {
        header('Location: ' . $uri, true);
        die();
    }

    public function init() {}
    public function postRun() {}
}