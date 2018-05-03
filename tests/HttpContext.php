<?php
use PhpMvc\HttpContextBase;

require_once 'HttpRequest.php';
require_once 'HttpResponse.php';

final class HttpContext extends HttpContextBase {

    public function __construct($routes, $ignoreRoutes, $serverVariables, $get = array(), $post = array(), $noOutputHeaders = false) {
        if (!isset($routes)) {
            $routesProperty = new \ReflectionProperty('\PhpMvc\RouteTable', 'routes');
            $routesProperty->setAccessible(true);
            $routes = $routesProperty->getValue(null);
        }

        if (!isset($ignoreRoutes)) {
            $ignoreRoutesProperty = new \ReflectionProperty('\PhpMvc\RouteTable', 'ignored');
            $ignoreRoutesProperty->setAccessible(true);
            $ignoreRoutes = $ignoreRoutesProperty->getValue(null);
        }

        parent::__construct(
            $routes,
            $ignoreRoutes,
            new HttpRequest($serverVariables, $get, $post), 
            new HttpResponse($noOutputHeaders)
        );
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

        return new HttpContext($routes, $ignoreRoutes, $serverVariables, $get, null, $noOutputHeaders);
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

        return new HttpContext($routes, $ignoreRoutes, $serverVariables, $get, $parameters, $noOutputHeaders);
    }

}