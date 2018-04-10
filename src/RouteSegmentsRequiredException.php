<?php
namespace PhpMvc;

/**
 * Occurs when the route does not contain all the required elements.
 */
class RouteSegmentsRequiredException extends \Exception {

    /**
     * Initializes a new instance of the RouteSegmentsRequiredException.
     * 
     * @param Route $route The route.
     */
    public function __construct($route) {
        parent::__construct(
            'The route "' . $this->name . '" must contain {controller} and {action}. ' .
            'If these elements are not present in the route template, then the default values for them must be defined in the route.'
        );
    }

}