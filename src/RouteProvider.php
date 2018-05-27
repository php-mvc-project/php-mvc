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
 * Defines the contract that represents the route provider.
 */
interface RouteProvider {

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
    function add($name, $template, $defaults = null, $constraints = null);

    /**
     * Removes all routes.
     * 
     * @return void
     */
    function clear();

    /**
     * Gets ignored routes list.
     * 
     * @return Route[]
     */
    function getIgnored();

    /**
     * Gets routes list.
     * 
     * @return Route[]
     */
    function getRoutes();

    /**
     * Gets options.
     * 
     * @return RouteOptions
     */
    function getOptions();

    /**
     * Sets options.
     * 
     * @return void
     */
    function setOptions($options);

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
    function insert($index, $name, $template, $defaults = null, $constraints = null);

    /**
     * Adds an URL pattern that should not be checked for matches against routes if a request URL meets the specified constraints.
     * 
     * @param string $template The URL pattern to be ignored.
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * 
     * @return void
     */
    function ignore($template, $constraints = null);

    /**
     * Initializes the provider.
     * 
     * @return void
     */
    function init();

    /**
     * Returns the first ignored route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    function matchIgnore($httpContext);

    /**
     * Returns the first route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    function matchRoute($httpContext);

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
    function tryBuildUrl($httpContext, $actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null);

}