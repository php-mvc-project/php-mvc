<?php
namespace PhpMvc;

/**
 * Represents current HTTP context.
 */
final class HttpContext extends HttpContextBase {

    /**
     * Initializes a new instance of the HttpContext for the current request.
     */
    public function __construct() {
        $routesProperty = new \ReflectionProperty('\PhpMvc\RouteTable', 'routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue(null);

        parent::__construct(new HttpRequest(), new HttpResponse(), $routes);
    }
    
}