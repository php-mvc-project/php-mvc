<?php
namespace PhpMvc;

/**
 * An ActionResult that returns a Found (302), 
 * Moved Permanently (301), Temporary Redirect (307), 
 * or Permanent Redirect (308) response with a Location header. 
 * Targets a controller action.
 */
class RedirectToActionResult extends RedirectResult {

    /**
     * Gets or sets the name of the action to use for generating the URL.
     * 
     * @var string
     */
    private $actionName;

    /**
     * Gets or sets the name of the controller to use for generating the URL.
     * 
     * @var string
     */
    private $controllerName;

    /**
     * Gets or sets the route data to use for generating the URL.
     * 
     * @var array
     */
    private $routeValues;

    /**
     * Gets or sets the fragment to add to the URL.
     * 
     * @var string
     */
    private $fragment;

    /**
     * Gets or sets a value that indicates whether the redirection should be permanent.
     * 
     * @var bool
     */
    private $permanent;

    /**
     * Gets or sets an indication that the redirect preserves the initial request method.
     * 
     * @var bool
     */
    private $preserveMethod;

    /**
     * Initializes a new instance of RedirectToActionResult.
     * 
     * @param string $actionName The name of the action to use for generating the URL.
     * @param string $controllerName The name of the controller to use for generating the URL.
     * @param array $routeValues The route data to use for generating the URL.
     * @param string $fragment The fragment to add to the URL.
     * @param bool $permanent A value that indicates whether the redirection should be permanent.
     * @param bool $preserveMethod If set to true, make the temporary redirect (307) or permanent redirect (308) preserve the intial request method.
     */
    public function __construct($actionName, $controllerName = null, $routeValues = null, $fragment = null, $permanent = false, $preserveMethod = false) {
        parent::__construct(null, $permanent, $preserveMethod);

        $this->actionName = !empty($actionName) ? $actionName : PHPMVC_ACTION;
        $this->controllerName = !empty($controllerName) ? $controllerName : PHPMVC_VIEW;
        $this->routeValues = $routeValues;
        $this->fragment = $fragment;

        $this->buildUrl();
    }

    private function buildUrl() {
        $this->url = '/?controller=' . $this->controllerName . '&action=' . $this->actionName;

        // $this->actionContext->route
        // header('Location: /' . $controllerName . '/' . $actionName);
    }

}