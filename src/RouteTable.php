<?php
namespace PhpMvc;

/**
 * Stores the URL routes for an application.
 */
class RouteTable {

    /**
     * Gets or sets a collection of routes.
     * 
     * @var RouteCollection
     */
    private static $routes;

    /**
     * Gets or sets a collection of ignored routes.
     * 
     * @var RouteCollection
     */
    private static $ignored;

    /**
     * Indicates whether the case of characters should be case-sensitive in determining the URL or not.
     * Default: false (case insensitive).
     * 
     * @var bool
     */
    private static $caseSensitive = false;

    /**
     * Sets the case sensitivity mode when searching for URL.
     * 
     * @param bool $value
     * 
     * @return void
     */
    public static function setCaseSensitive($value) {
        RouteTable::$caseSensitive = $value;
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
    public static function add($name, $template, $defaults = null, $constraints = null) {
        self::ensureRoutes();

        $route = new Route();

        $route->name = $name;
        $route->template = $template;
        $route->defaults = $defaults;
        $route->constraints = $constraints;

        self::$routes->add($route);
    }

    /**
     * Adds an URL pattern that should not be checked for matches against routes if a request URL meets the specified constraints.
     * 
     * @param string $template The URL pattern to be ignored.
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * 
     * @return void
     */
    public static function ignore($template, $constraints = null) {
        self::ensureRoutes();

        $route = new Route();

        // $route->name = '__' . $template . '_' . rand(1, 1000);
        $route->template = $template;
        $route->constraints = $constraints;
        $route->ignore = true;
        
        self::$ignored->add($route);
    }

    /**
     * Removes all routes.
     * 
     * @return void
     */
    public static function clear() {
        self::ensureRoutes();
        self::$routes->clear();
        self::$ignored->clear();
    }

    /**
     * Initializes the required variables.
     * 
     * @return void
     */
    private static function ensureRoutes() {
        if (self::$routes == null) {
            self::$routes = new RouteCollection();
        }

        if (self::$ignored == null) {
            self::$ignored = new RouteCollection();
        }
    }

}