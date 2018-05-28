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
     * Inserts a route into the collection at the specified index.
     * 
     * @param int $index The zero-based index at which route should be inserted.
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
    public function insert($index, $name, $template, $defaults = null, $constraints = null) {
        $route = new Route();

        $route->name = $name;
        $route->template = $template;
        $route->defaults = $defaults;
        $route->constraints = $constraints;

        $this->routes->insert($index, $route);
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
     * Initializes the provider.
     * 
     * @return void
     */
    public function init() {
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
     * Tries to build a url with the specified parameters.
     * 
     * @param HttpContextBase $httpContext The context of the request.
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * 
     * @return string
     */
    public function tryBuildUrl($httpContext, $actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        $options = $this->getOptions();

        $result = '';

        if (!empty($schema)) {
            $result .= $schema . '://';

            if (empty($host)) {
                $result .= $httpContext->getRequest()->server('HTTP_HOST') . '/';
            }
        }

        if (!empty($host)) {
            if (empty($schema)) {
                if ($httpContext->getRequest()->isSecureConnection()) {
                    $schema = 'https';
                }
                else {
                    $schema = 'http';
                }

                $result .= $schema . '://';
            }

            $result .= $host . '/';
        }

        if (empty($result)) {
            $result = '/';
        }

        $routeValues = isset($routeValues) ? $routeValues : array();

        if (empty($controllerName)) {
            // use controller of the current route
            $controllerName = $httpContext->getRoute()->values['controller'];
        }

        // search route
        $routes = $httpContext->getRoutes();
        $possibleRoute = null;
        $foundRoute = null;

        foreach ($routes as $route) {
            if (empty($route->defaults)) {
                continue;
            }

            if (!empty($route->defaults['controller']) && $route->defaults['controller'] == $controllerName) {
                $segments = $route->getSegments(true);
                $possibleRoute = !empty($segments['action']) ? $route : null;

                if (empty($segments['action']) && !empty($route->defaults['action']) && $route->defaults['action'] != $actionName) {
                    continue;
                }

                $foundRoute = $route;

                break;
            }
        }

        if ($foundRoute !== null) {
            $route = $foundRoute;
        }
        elseif ($possibleRoute != null) {
            $route = $possibleRoute;
        }

        $segments = $route->getSegments();
        $default = array();
        $path = '';

        foreach ($segments as $segment) {
            $isDefault = false;
            $path .= $segment->after;

            if (empty($segment->name)) {
                $path .= $segment->pattern;
            }
            elseif ($segment->name == 'controller' && !empty($controllerName)) {
                $path .= $controllerName;
                $isDefault = (!empty($segment->default) && mb_strtolower($segment->default) === mb_strtolower($controllerName));
            }
            elseif ($segment->name == 'controller' && empty($controllerName) && !empty($route->values['controller'])) {
                $path .= $route->values['controller'];
                $isDefault = (!empty($segment->default) && mb_strtolower($segment->default) === mb_strtolower($route->values['controller']));
            }
            elseif ($segment->name == 'action') {
                $path .= $actionName;
                $isDefault = (!empty($segment->default) && mb_strtolower($segment->default) === mb_strtolower($actionName));
            }
            else {
                if (!empty($routeValues[$segment->name])) {
                    $path .= $routeValues[$segment->name];
                    unset($routeValues[$segment->name]);
                    $isDefault = false;
                }
                else
                {
                    if ($segment->default !== UrlParameter::OPTIONAL) {
                        $path .= $segment->default;
                    }
                    else {
                        $isDefault = true;
                    }
                }
            }

            $path .= $segment->before;

            $path .= '/';

            if ($isDefault) {
                $isDefault = empty($segment->before);
            }

            $default[] = $isDefault;
        }

        $path = rtrim($path, '/');

        if ($options->removeLastSegmentIfValueIsDefault === true) {
            $resultSegments = explode('/', $path);

            if (count($default) > count($resultSegments)) {
                $default = array_slice($default, 0, count($resultSegments));
            }

            for ($i = count($resultSegments) - 1; $i >= 0; --$i) {
                if ($default[$i]) {
                    unset($resultSegments[$i]);
                }
                else {
                    break;
                }
            }

            $path = implode('/', $resultSegments);
        }

        $result .= $path;

        if ($options->lowercaseUrls === true) {
            $result = mb_strtolower($result);
        }

        if ($options->appendTrailingSlash === true && $result !== '/') {
            $result .= '/';
        }

        if (!empty($routeValues)) {
            $result .= '?' . http_build_query($routeValues);
        }

        if (!empty($fragment)) {
            $result .= '#' . $fragment;
        }

        return $result;
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
                '__route_segments_0_' . $route->template,
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
                        if (empty($values[$name]) && $defaultValue != UrlParameter::OPTIONAL) {
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