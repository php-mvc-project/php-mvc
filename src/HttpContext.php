<?php
namespace PhpMvc;

/**
 * Represents current HTTP context.
 */
final class HttpContext extends HttpContextBase {

    /**
     * Gets or sets current HttpContext.
     * 
     * @var HttpContextBase
     */
    private static $current;

    /**
     * Initializes a new instance of the HttpContext for the current request.
     */
    public function __construct() {
        $routes = InternalHelper::getStaticPropertyValue('\PhpMvc\RouteTable', 'routes');
        $ignored = InternalHelper::getStaticPropertyValue('\PhpMvc\RouteTable', 'ignored');

        parent::__construct($routes, $ignored, new HttpRequest(), new HttpResponse());
    }

    /**
     * Returs current HttpContext.
     * 
     * @return HttpContextBase
     */
    public static function getCurrent() {
        return self::$current;
    }

}