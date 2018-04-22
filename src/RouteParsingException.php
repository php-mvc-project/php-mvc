<?php
namespace PhpMvc;

/**
 * Occurs when errors are detected during route parsing.
 */
class RouteParsingException extends \Exception {

    /**
     * Initializes a new instance of the RouteSegmentsRequiredException.
     * 
     * @param Route $route The route.
     * @param string|null $message The error message.
     */
    public function __construct($route, $message = null) {
        if (!empty($message)) {
            parent::__construct($message);
        }
        else {
            parent::__construct('One or more errors occurred while parsing the route "' . $route->name . '"');
        }
    }

}