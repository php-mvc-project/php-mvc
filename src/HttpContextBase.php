<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpMvc;

/**
 * Serves as the base class for classes that contain HTTP-specific information about an individual HTTP request.
 */
abstract class HttpContextBase {

    /**
     * When overridden in a derived class, gets the Cache object for the current request.
     * 
     * @var Cache
     */
    protected $cache;

    /**
     * When overridden in a derived class, gets the HttpRequest object for the current HTTP request.
     * 
     * @var HttpRequestBase
     */
    protected $request;

    /**
     * When overridden in a derived class, gets the HttpResponse object for the current HTTP request.
     * 
     * @var HttpResponseBase
     */
    protected $response;

    /**
     * Gets the HttpSessionProvider for the current HTTP request.
     * 
     * @var HttpSessionProvider
     */
    protected $session;

    /**
     * The routes provider.
     * 
     * @var RouteProvider
     */
    protected $routes;

    /**
     * Current route.
     * 
     * @var Route
     */
    protected $route;

    /**
     * Indicates that the request must be ignored.
     */
    protected $ignoreRoute = null;

    /**
     * The initial timestamp of the current HTTP request.
     * 
     * @var int
     */
    protected $timestamp;

    /**
     * Initializes a new instance of the HttpContextBase.
     * 
     * @param HttpContextInfo $info Context info.
     */
    public function __construct($info) {
        if (!isset($info) || !$info instanceof HttpContextInfo) {
            throw new \Exception('The $info type must be the base of "\PhpMvc\HttpContextInfo".');
        }

        $date = new \DateTime();
        $this->timestamp = $date->getTimestamp();

        $this->routes = $info->routeProvider;
        $this->cache = $info->cacheProvider;
        $this->request = $info->request;
        $this->response = $info->response;
        $this->session = $info->session;
    }

    /**
     * Gets cache provider.
     * 
     * @return Cache
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * Gets the HttpRequestBase object.
     * 
     * @return HttpRequestBase
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Gets the HttpResponseBase object.
     * 
     * @return HttpResponseBase
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Gets the HttpSessionProvider.
     * 
     * @return array|mixed
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Gets list of routes.
     * 
     * @return RouteCollection
     */
    public function getRoutes() {
        return $this->routes->getRoutes();
    }

    /**
     * Returns a route that is comparable to the current request context.
     * 
     * @return Route|null
     */
    public function getRoute() {
        if ($this->route === null && $this->isIgnoredRoute() === false) {
            $this->route = $this->routes->matchRoute($this);
        }

        return $this->route;
    }

    /**
     * Returns TRUE if the current route is to be ignored.
     * 
     * @return bool
     */
    public function isIgnoredRoute() {
        if ($this->ignoreRoute === null) {
            $this->ignoreRoute = ($this->route = $this->routes->matchIgnore($this)) !== null;
        }

        return $this->ignoreRoute;
    }

    /**
     * Gets the initial timestamp of the current HTTP request.
     * 
     * @return int
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    
    /**
     * Tries to build a url with the specified parameters.
     * 
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * 
     * @return string
     */
    public function tryBuildUrl($actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        return $this->routes->tryBuildUrl($this, $actionName, $controllerName, $routeValues, $fragment, $schema, $host);
    }

}