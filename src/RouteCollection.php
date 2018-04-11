<?php
namespace PhpMvc;

/**
 * Represents a collection of routes and provides a route search for the request context.
 */
class RouteCollection implements \ArrayAccess, \Iterator {

    /**
     * Index of the current item.
     * 
     * @var int
     */
    private $position = 0;

    /**
     * List of routes.
     * 
     * @var Route[]
     */
    private $routes = array();

    /**
     * Route options.
     * 
     * @var RouteOptions
     */
    private $options;

    public function __construct() {
        $this->options = new RouteOptions();
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
     * Gets options.
     * 
     * @return void
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Adds a route to collection.
     * 
     * @param Route $route The route to add.
     * 
     * @return void
     */
    public function add($route) {
        $this->validateRoute($route);
        $this->routes[] = $route;
    }

    /**
     * Remove all routes.
     * 
     * @return void
     */
    public function clear() {
        $this->routes = array();
    }

    /**
     * Returns the first route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    public function getRoute($httpContext) {
        $csm = false ? '' : 'i';
        $path = trim($httpContext->getRequest()->getRequestUri(), '/');
        
        if (($qsIndex = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $qsIndex);
        }

        if ($path == 'index.php') {
            $path = '';
        }

        foreach($this->routes as $route) {
            $segments = $route->getSegments();

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

                return $result;
            }
        }

        return null;
    }

    /**
     * Verifies that the data in the route instance is correct.
     * 
     * @param Route $route The route to check.
     * 
     * @return void
     */
    private function validateRoute($route) {
        if (!$route instanceof Route) {
            throw new \Exception('A type of Route or a derivative is expected.');
        }
        
        if (empty($route->name)) {
            throw new \Exception('The name of the route is expected. The value must not be empty.');
        }

        if (!$this->isUniqueRouteName($route->name)) {
            throw new \Exception('The name "' . $route->name . '" is already used for another route. Specify a unique name.');
        }

        if (empty($route->template)) {
            throw new \Exception('The route "' . $route->name . '" does not specify a template. Please specify a template.');
        }
    }

    /**
     * Checks the uniqueness of the route name.
     * 
     * @param string $name Name to check.
     * 
     * @return bool
     */
    private function isUniqueRouteName($name) {
        foreach ($this->routes as $route) {
            if (mb_strtolower($route->name) == mb_strtolower($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Whether or not an offset exists.
     *
     * @param string|int $offset An offset to check for.
     * 
     * @return bool
     */
    function offsetExists($offset)
    {
      return isset($this->routes[$offset]);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * 
     * @return mixed
     */
    function offsetGet($offset)
    {
      return isset($this->routes[$offset]) ? $this->routes[$offset] : null;
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * 
     * @return void
     */
    function offsetSet($offset, $value)
    {
      $this->routes[$offset] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset.
     * 
     * @return void
     */
    function offsetUnset($offset)
    {
      unset($this->routes[$offset]);
    }

    /**
     * Rewinds the Iterator to the first element.
     * 
     * @return void
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Returns the current element.
     * 
     * @return Route
     */
    public function current() {
        return $this->routes[$this->position];
    }

    /**
     * Returns the key of the current element
     * 
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * Move forward to next element
     * 
     * @return void
     */
    public function next() {
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     * 
     * @return bool
     */
    public function valid() {
        return isset($this->routes[$this->position]);
    }

}