<?php
use PhpMvc\InternalHelper;
use PhpMvc\HttpContextBase;
use PhpMvc\IdleCacheProvider;
use PhpMvc\DefaultRouteProvider;

require_once 'HttpRequest.php';
require_once 'HttpResponse.php';
require_once 'HttpContextInfo.php';

final class HttpContext extends HttpContextBase {

    public function __construct($info) {
        if ($info->routeProvider === null) {
            $info->routeProvider = new DefaultRouteProvider();
        }

        if ($info->cacheProvider === null) {
            $info->cacheProvider = new IdleCacheProvider();
        }

        $info->request = new HttpRequest($info->serverVariables, $info->get, $info->post);
        $info->response = new HttpResponse($info->noOutputHeaders);

        parent::__construct($info);
    }

    /**
     * 
     * @return HttpContext
     */
    public function setCacheProvider($cacheProvider) {
        $this->cache = $cacheProvider;

        return $this;
    }

    /**
     * 
     * @return HttpContext
     */
    public function setRoutes($routeProvider) {
        $this->routes = $routeProvider;

        return $this;
    }

    /**
     * 
     * @return HttpContext
     */
    public function useDefaultRoute() {
        $routeProvider = new DefaultRouteProvider();
        $routeProvider->add('default', '{controller=Home}/{action=index}/{id?}');

        $this->routes = $routeProvider;

        return $this;
    }

    public function noOutputHeaders() {
        $this->response->setNoOutputHeaders(true);

        return $this;
    }

    public function outputHeaders() {
        $this->response->setNoOutputHeaders(false);

        return $this;
    }

    public static function getWithRoute($url, $routes, $ignoreRoutes = null, $serverVariables = null, $noOutputHeaders = false) {
        $serverVariables = ($serverVariables != null ? $serverVariables : array());

        $url = parse_url($url);

        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
        }

        if (empty($url['host'])) {
            $url['host'] = 'localhost';
        }

        if (empty($url['path'])) {
            $url['path'] = 'path';
        }
        
        if ($url['scheme'] === 'https') {
            $serverVariables['HTTPS'] = true;
            $serverVariables['SERVER_PORT'] = 443;
        }
        else {
            $serverVariables['SERVER_PORT'] = 80;
        }

        $serverVariables['REQUEST_METHOD'] = 'GET';
        $serverVariables['HTTP_HOST'] = $url['host'];
        $serverVariables['REQUEST_URI'] = $url['path'] . (!empty($url['query']) ? '?' . $url['query'] : '');

        if (!empty($url['query'])) {
            $serverVariables['QUERY_STRING'] = $url['query'];
            parse_str($url['query'], $get);
        }
        else {
            $get = array();
        }

        $info = new HttpContextInfo();
        $info->routeProvider = new DefaultRouteProvider();
        $info->noOutputHeaders = $noOutputHeaders;
        $info->serverVariables = $serverVariables;
        $info->get = $get;

        if (!empty($routes)) {
            foreach ($routes as $route) {
                $info->routeProvider->add($route->name, $route->template, $route->defaults, $route->constraints);
            }
        }

        if (!empty($ignoreRoutes)) {
            foreach ($ignoreRoutes as $route) {
                $info->routeProvider->ignore($route->template, $route->constraints);
            }
        }

        return new HttpContext($info);
    }

    public static function get($url, $serverVariables = null, $noOutputHeaders = false) {
        return self::getWithRoute($url, null, null, $serverVariables, $noOutputHeaders);
    }

    public static function post($url, $parameters = null, $routes = null, $ignoreRoutes = null, $serverVariables = null, $noOutputHeaders = false) {
        $serverVariables = ($serverVariables != null ? $serverVariables : array());

        $url = parse_url($url);

        if (empty($url['scheme'])) {
            $url['scheme'] = 'http';
        }

        if (empty($url['host'])) {
            $url['host'] = 'localhost';
        }

        if (empty($url['path'])) {
            $url['path'] = 'path';
        }
        
        if ($url['scheme'] === 'https') {
            $serverVariables['HTTPS'] = true;
            $serverVariables['SERVER_PORT'] = 443;
        }
        else {
            $serverVariables['SERVER_PORT'] = 80;
        }

        $serverVariables['REQUEST_METHOD'] = 'POST';
        $serverVariables['HTTP_HOST'] = $url['host'];
        $serverVariables['REQUEST_URI'] = $url['path'] . (!empty($url['query']) ? '?' . $url['query'] : '');

        if (!empty($url['query'])) {
            $serverVariables['QUERY_STRING'] = $url['query'];
            parse_str($url['query'], $get);
        }
        else {
            $get = array();
        }

        $info = new HttpContextInfo();
        $info->routeProvider = new DefaultRouteProvider();
        $info->noOutputHeaders = $noOutputHeaders;
        $info->serverVariables = $serverVariables;
        $info->get = $get;
        $info->post = $parameters;

        if (!empty($routes)) {
            foreach ($routes as $route) {
                $info->routeProvider->add($route->name, $route->template, $route->defaults, $route->constraints);
            }
        }

        if (!empty($ignoreRoutes)) {
            foreach ($ignoreRoutes as $route) {
                $info->routeProvider->ignore($route->template, $route->constraints);
            }
        }

        return new HttpContext($info);
    }

}