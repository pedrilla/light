<?php

declare(strict_types = 1);

namespace Light;

use Light\Filter\FilterAbstract;

/**
 * Class Request
 * @package Light
 */
class Request
{
    /**
     * Supported request method
     */
    const METHOD_GET    = 'get';
    const METHOD_POST   = 'post';
    const METHOD_PUT    = 'put';
    const METHOD_DELETE = 'delete';

    /**
     * @var array
     */
    private $_postParams = [];

    /**
     * @var array
     */
    private $_getParams = [];

    /**
     * @var array
     */
    private $_params = [];

    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @var string
     */
    private $_method = null;

    /**
     * @var string
     */
    private $_uri = null;

    /**
     * @var string
     */
    private $_uriParams = null;

    /**
     * @var null
     */
    private $_uriRequest = null;

    /**
     * @var string
     */
    private $_domain = null;

    /**
     * @var string
     */
    private $_scheme = null;

    /**
     * @var int
     */
    private $_port = null;

    /**
     * @var string
     */
    private $_ip = null;

    /**
     * @var bool
     */
    private $_isAjax = false;

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->_isAjax;
    }

    /**
     * @param bool $isAjax
     */
    public function setIsAjax(bool $isAjax): void
    {
        $this->_isAjax = $isAjax;
    }

    /**
     * @return void
     */
    public function fillRequestFromServer()
    {
        $this->_getParams = $_GET;
        $this->_postParams = $_POST;
        $this->_params = $_REQUEST;

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) == 'HTTP_') {
                $this->_headers[str_replace('_', '-', strtolower(substr($key, 5)))] = $value;
            }
        }

        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->_uri = urldecode($_SERVER['REQUEST_URI']);
        $this->_domain = $_SERVER['HTTP_HOST'];
        $this->_scheme = $_SERVER['REQUEST_SCHEME'];
        $this->_port = (int)$_SERVER['SERVER_PORT'];
        $this->_ip = $_SERVER['REMOTE_ADDR'];

        $this->_uriRequest = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->_uriParams = explode('?', $_SERVER['REQUEST_URI'])[1] ?? '';

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->_isAjax = true;
        }
    }

    /**
     * @return void
     */
    public function fillRequestFromCli()
    {
        $this->_getParams = $_GET;

        global $argv;

        $route = null;

        foreach ($argv as $arg) {

            $e = explode("=", $arg);

            if ($e[0] == 'route') {
                $route = $e[1];
                continue;
            }

            if (count($e) == 2) {
                $this->_getParams[$e[0]] = $e[1];
            }
        }

        $this->_uri = '/' . $route;
        $this->_domain = 'cli';
    }

    /**
     * @param string $key
     * @param null $default
     * @param FilterAbstract[] $filters
     * @return string|int|mixed|null|array
     */
    public function getGet(string $key, $default = null, array $filters = [])
    {
        $value = $this->_getParams[$key] ?? $default;

        foreach ($filters as $filter) {
            $value = $filter->filter($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getGetAll() : array
    {
        return $this->_getParams;
    }

    /**
     * @param string $key
     * @param null $default
     * @param FilterAbstract[] $filters
     *
     * @return string|int|mixed|null|array
     */
    public function getPost(string $key, $default = null, array $filters = [])
    {
        $value = $this->_postParams[$key] ?? $default;

        foreach ($filters as $filter) {

            $filter = new $filter();

            $value = $filter->filter($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getPostAll() : array
    {
        return $this->_postParams;
    }

    /**
     * @param string $name
     * @param null $default
     * @param FilterAbstract[] $filters
     *
     * @return string|int|mixed|null|array
     */
    public function getParam(string $name, $default = null, array $filters = [])
    {
        $value = $this->_params[$name] ?? $default;

        foreach ($filters as $filter) {

            $filter = new $filter();

            $value = $filter->filter($value);
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getParams() : array
    {
        return $this->_params;
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->_headers;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getHeader(string $key)
    {
        return $this->_headers[$key] ?? null;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value)
    {
        $this->_headers[$key] = $value;
    }

    /**
     * @return array
     */
    public function getXHeaders()
    {
        $xHeaders = [];

        foreach ($this->_headers as $name => $value) {
            if (substr($name, 0, 2) == 'x-') {
                $xHeaders[$name] = $value;
            }
        }

        return $xHeaders;
    }

    /**
     * @return bool
     */
    public function isGet() : bool
    {
        return $this->_method == self::METHOD_GET;
    }

    /**
     * @return bool
     */
    public function isPost() : bool
    {
        return $this->_method == self::METHOD_POST;
    }

    /**
     * @return bool
     */
    public function isPut() : bool
    {
        return $this->_method == self::METHOD_PUT;
    }

    /**
     * @return bool
     */
    public function isDelete() : bool
    {
        return $this->_method == self::METHOD_DELETE;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @param string $method
     * @throws Exception\RequestMethodIsNotSupported
     */
    public function setMethod(string $method)
    {
        if (!in_array($method, [self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE])) {
            throw new Exception\RequestMethodIsNotSupported($method);
        }

        $this->_method = $method;
    }

    /**
     * @return string
     */
    public function getUri() : string
    {
        return $this->_uri;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri)
    {
        $this->_uri = $uri;
    }

    /**
     * @return string
     */
    public function getDomain() : string
    {
        return $this->_domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain)
    {
        $this->_domain = $domain;
    }

    /**
     * @return string
     */
    public function getScheme() : string
    {
        return $this->_scheme;
    }

    /**
     * @param string $scheme
     */
    public function setScheme(string $scheme)
    {
        $this->_scheme = $scheme;
    }

    /**
     * @return int
     */
    public function getPort() : int
    {
        return $this->_port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port)
    {
        $this->_port = $port;
    }

    /**
     * @return string
     */
    public function getIp() : string
    {
        return $this->_ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip)
    {
        $this->_ip = $ip;
    }

    /**
     * @param string $key
     * @param $value
     * @param bool $replace
     */
    public function setGetParam(string $key, $value, bool $replace = false)
    {
        if ($replace || !isset($this->_getParams[$key])) {
            $this->_getParams[$key] = $value;
            return;
        }
    }

    /**
     * @return string
     */
    public function getUriParams(): string
    {
        return $this->_uriParams;
    }

    /**
     * @param string $uriParams
     */
    public function setUriParams(string $uriParams): void
    {
        $this->_uriParams = $uriParams;
    }

    /**
     * @return null
     */
    public function getUriRequest()
    {
        return $this->_uriRequest;
    }

    /**
     * @param null $uriRequest
     */
    public function setUriRequest($uriRequest): void
    {
        $this->_uriRequest = $uriRequest;
    }
}
