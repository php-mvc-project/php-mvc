<?php
namespace PhpMvc;

/**
 * Serves as the base class for classes that contain HTTP-specific information about an individual HTTP request.
 */
abstract class HttpContextBase {

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
     * Route collection.
     * 
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Current route.
     * 
     * @var Route
     */
    protected $route;

    /**
     * Indicates that the request should be ignored.
     */
    protected $ignore;

    /**
     * The initial timestamp of the current HTTP request.
     * 
     * @var int
     */
    protected $timestamp;

    /**
     * Initializes a new instance of the HttpContextBase.
     * 
     * @param RouteCollection $routes The routes list.
     * @param RouteCollection $ignoreRoutes List of routes to ignore.
     * @param HttpRequestBase $request The reqiest.
     * @param HttpResponseBase $response The response.
     */
    public function __construct($routes, $ignoreRoutes, $request, $response) {
        if (!isset($routes) || !$routes instanceof RouteCollection) {
            throw new \Exception('The $routes is requred and type must be derived from "\PhpMvc\RouteCollection".');
        }

        if (!isset($request) || !$request instanceof HttpRequestBase) {
            throw new \Exception('The $request is requred and type must be derived from "\PhpMvc\HttpRequestBase".');
        }

        if (!isset($response) || !$response instanceof HttpResponseBase) {
            throw new \Exception('The $response is requred and type must be derived from "\PhpMvc\HttpResponseBase".');
        }

        $date = new \DateTime();
        $this->timestamp = $date->getTimestamp();

        $this->routes = $routes;
        $this->request = $request;
        $this->response = $response;

        if (!($this->ignore = (isset($ignoreRoutes) && $ignoreRoutes instanceof RouteCollection && ($this->route = $ignoreRoutes->getRoute($this)) !== null))) {
            $this->route = $routes->getRoute($this);
        }
    }

    /**
     * Gets list of routes.
     * 
     * @return RouteCollection
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Gets route options.
     * 
     * @return RouteOptions
     */
    public function getRouteOptions() {
        return $this->routes->getOptions();
    }

    /**
     * Gets request.
     * 
     * @return HttpRequestBase
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Gets response.
     * 
     * @return HttpResponseBase
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Returns a route that is comparable to the current request context.
     * 
     * @return Route|null
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Returns TRUE if the current route is to be ignored.
     * 
     * @return bool
     */
    public function isIgnoredRoute() {
        return $this->ignore;
    }

    /**
     * Gets the initial timestamp of the current HTTP request.
     * 
     * @return int
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

}