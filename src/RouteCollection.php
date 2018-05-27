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
 * Represents a collection of routes and provides a route search for the request context.
 */
class RouteCollection implements \ArrayAccess, \Iterator {

    /**
     * Unique collection name.
     * 
     * @var string
     */
    protected $name;

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
     * Initializes a new named instance of the collection.
     * 
     * @param string $name The name of instance.
     */
    public function __construct($name) {
        $this->name = $name;
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
     * Inserts a route into the collection at the specified index.
     * 
     * @param int $index The zero-based index at which route should be inserted.
     * @param Route $route The route to add.
     * 
     * @return void
     */
    public function insert($index, $route) {
        $this->validateRoute($route);
        array_splice($this->routes, $index, 0, array($route));
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
     * Gets name of current instance.
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
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
        
        if ($route->ignore !== true && empty($route->name)) {
            throw new \Exception('The name of the route is expected. The value must not be empty.');
        }

        if ($route->ignore !== true && !$this->isUniqueRouteName($route->name)) {
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