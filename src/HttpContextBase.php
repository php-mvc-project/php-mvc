<?php
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
     * An associative array containing session variables available to the current script.
     * 
     * @var array
     */
    protected $session;

    /**
     * The routes collection.
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
     * Gets the session variables.
     * 
     * @param string|null $key The key to get. Default: null - all variables.
     * 
     * @return array|mixed
     */
    public function getSession($key = null) {
        return InternalHelper::getSingleKeyOrAll($this->session, $key);
    }

    /**
     * Sets the variable to the session.
     * 
     * @param string $key The key to set.
     * @param string $value The value to set.
     * 
     * @return void
     */
    public function setSession($key, $value) {
        $this->session[$key] = $value;
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
     * Gets route options.
     * 
     * @return RouteOptions
     */
    public function getRouteOptions() {
        return $this->routes->getOptions();
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

}