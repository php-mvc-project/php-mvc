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
    public function __construct($info) {
        parent::__construct($info);
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