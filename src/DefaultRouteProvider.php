<?php
namespace PhpMvc;

/**
 * Represents default route provider.
 */
final class DefaultRouteProvider implements RouteProvider {

    /**
     * Gets or sets a collection of ignored routes.
     * 
     * @var RouteCollection
     */
    private $ignored;

    /**
     * Gets or sets a collection of routes.
     * 
     * @var RouteCollection
     */
    private $routes;

    /**
     * Route options.
     * 
     * @var RouteOptions
     */
    private $options;

    /**
     * Initializes a new default route provider instance.
     * 
     * @param RouteOptions $routeOptions The options of the router.
     */
    public function __construct($routeOptions = null) {
        $this->routes = new RouteCollection('rules');
        $this->ignored = new RouteCollection('ignored');
        $this->options = ($routeOptions !== null ? $routeOptions : new RouteOptions());
    }

    /**
     * Gets ignored routes list.
     * 
     * @return Route[]
     */
    public function getIgnored() {
        return $this->ignored;
    }

    /**
     * Gets routes list.
     * 
     * @return Route[]
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Gets options.
     * 
     * @return RouteOptions
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Sets options.
     * 
     * @return void
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * Returns the first ignored route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    public function matchIgnore($httpContext) {
        return $this->match($this->ignored, $httpContext);
    }

    /**
     * Returns the first route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    public function matchRoute($httpContext) {
        return $this->match($this->routes, $httpContext);
    }

    /**
     * Adds a rule.
     * 
     * @param string $name The unique name of the route.
     * @param string $template The template by which the route will be searched.
     * Use curly braces to denote the elements of the route.
     * For example: {controller}/{action}/{id}
     * {controller=Home}/{action=index}/{id?}
     * @param array $defaults An associative array containing the default values for the elements defined in the $template.
     * For example, $template is {controller}/{action}/{id}
     * $defaults = array('controller' => 'Home', 'action' => 'index', id => \PhpMvc\UrlParameter.OPTIONAL)
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * For example, $template is {controller}/{action}/{id}
     * $constraints = array('id' => '\w+')
     * 
     * @return void
     */
    public function add($name, $template, $defaults = null, $constraints = null) {
        $route = new Route();

        $route->name = $name;
        $route->template = $template;
        $route->defaults = $defaults;
        $route->constraints = $constraints;

        $this->routes->add($route);
    }

    /**
     * Adds an URL pattern that should not be checked for matches against routes if a request URL meets the specified constraints.
     * 
     * @param string $template The URL pattern to be ignored.
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * 
     * @return void
     */
    public function ignore($template, $constraints = null) {
        $route = new Route();

        $route->template = $template;
        $route->constraints = $constraints;
        $route->ignore = true;

        $this->ignored->add($route);
    }

    /**
     * Removes all routes.
     * 
     * @return void
     */
    public function clear() {
        $this->routes->clear();
        $this->ignored->clear();
    }

    /**
     * Returns from the specified collection the first route similar to the current url.
     * 
     * @param RouteCollection $routes The route collection.
     * @param HttpContextBase $httpContext The context of the request.
     * 
     * @return Route|null
     */
    private function match($routes, $httpContext) {
        $csm = false ? '' : 'i';
        $path = trim($httpContext->getRequest()->rawUrl(), '/');
        
        if (($qsIndex = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $qsIndex);
        }

        if ($path == 'index.php') {
            $path = '';
        }

        $cache = $httpContext->getCache();
        $cacheKey = '__route_' . $routes->getName() . '_' . $path;

        if ($cache->get($cacheKey) !== null) {
            return $cache->get($cacheKey);
        }

        foreach($routes as $route) {
            $segments = $cache->getOrAdd(
                '__route_segments_' . $route->template,
                $route->getSegments(),
                1200
            );

            // make final pattern
            $pattern = '';

            foreach ($segments as $segment) {
                if (!empty($segment->before)) {
                    $pattern .= $segment->before;
                }

                $pattern .= $segment->pattern;

                if (!empty($segment->after)) {
                    $pattern .= $segment->after;
                }

                if (empty($segment->glued)) {
                    if ($segment->optional || !empty($segment->end) || !empty($segment->preEnd)) {
                        $pattern .= '(\/|)';
                    }
                    else {
                        $pattern .= '\/';
                    }
                }
            }

            // test url
            if (preg_match('/^' . $pattern . '$/s' . $csm, $path, $matches) === 1) {
                // match is found
                $result = clone $route;
                $values = array();

                foreach ($segments as $segment) {
                    $name = $segment->name;

                    if (!empty($matches[$name])) {
                        if (empty($values[$name])) {
                            $values[$name] = $matches[$name];
                        }
                    }

                    if (!empty($segment->default)) {
                        if (empty($values[$name]) && $segment->default != UrlParameter::OPTIONAL) {
                            $values[$name] = $segment->default;
                        }
                    }
                }

                if (!empty($route->defaults)) {
                    foreach ($route->defaults as $name => $defaultValue) {
                        if (empty($values[$name])) {
                            $values[$name] = $defaultValue;
                        }
                    }
                }

                if (true) {
                    $values = array_map('strtolower', $values);
                }

                $result->values = $values;

                $cache->add($cacheKey, $result, 1200);

                return $result;
            }
        }

        $cache->add($cacheKey, null, 1200);

        return null;
    }

}