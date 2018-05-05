<?php
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

}