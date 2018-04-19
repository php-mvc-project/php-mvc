<?php
namespace PhpMvc;

/**
 * Represents controller context of the current request.
 */
class ControllerContext {

    /**
     * Gets or sets context of the current request.
     * 
     * @var HttpContextBase
     */
    private $httpContext;

    /**
     * Gets or sets the Route for the current request.
     * 
     * @var Route
     */
    private $route;

    /**
     * Gets or sets the controller.
     * 
     * @var Controller
     */
    private $controller;

    /**
     * Initializes a new instance of the ControllerContext for the current request.
     * 
     * @param Controller $controller The controller instance.
     */
    public function __construct($controller) {
        $this->controller = $controller;
        $this->httpContext = InternalHelper::invokeMethod($controller, 'getHttpContext');
        $this->route = InternalHelper::invokeMethod($controller, 'getRoute');
    }

    /**
     * Returns context of the current request.
     * 
     * @return HttpContextBase
     */
    public function getHttpContext() {
        return $this->httpContext;
    }

    /**
     * Returns current Route.
     * 
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Returns controller instance.
     * 
     * @return Controller
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Gets the HttpRequestBase object for the current request.
     * 
     * @return HttpRequestBase
     */
    protected function getRequest() {
        return $this->httpContext->getRequest();
    }

}