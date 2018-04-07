<?php
namespace PhpMvc;

/**
 * Context object for execution of action which has been selected as part of an HTTP request.
 */
class ActionContext {

    /**
     * Gets or sets context of the current request.
     * 
     * @var HttpContextBase
     */
    public $httpContext;

    /**
     * Gets or sets the Route for the current request.
     * 
     * @var Route
     */
    public $route;

    /**
     * Gets or sets the controller.
     * 
     * @var Controller
     */
    public $controller;

    /**
     * Gets or sets the state of model.
     * 
     * @var ModelState
     */
    public $modelState;

    /**
     * Gets or sets action arguments.
     * 
     * @var array
     */
    public $arguments;

    /** 
     * Gets of sets name of action.
     * 
     * @var string
     */
    public $actionName;

    /**
     * Initializes a new instance of the ActionContext for the current request.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     */
    public function __construct($httpContext) {
        $this->httpContext = $httpContext;
        $this->route = $httpContext->getRoute();
        $this->modelState = new ModelState();
    }

}