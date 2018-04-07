<?php
use PhpMvc\HttpContextBase;

require_once 'HttpRequest.php';
require_once 'HttpResponse.php';

final class HttpContext extends HttpContextBase {

    public function __construct($routes, $serverVariables, $get = array(), $post = array()) {
        if (!isset($routes)) {
            $routesProperty = new \ReflectionProperty('\PhpMvc\RouteTable', 'routes');
            $routesProperty->setAccessible(true);
            $routes = $routesProperty->getValue(null);
        }

        parent::__construct(
            $routes,
            new HttpRequest($serverVariables, $get, $post), 
            new HttpResponse()
        );
    }
    
}