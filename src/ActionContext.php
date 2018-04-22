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
    protected $httpContext;

    /**
     * Gets or sets the Route for the current request.
     * 
     * @var Route
     */
    protected $route;

    /**
     * Gets or sets the controller.
     * 
     * @var Controller
     */
    protected $controller;

    /**
     * Gets or sets the state of model.
     * 
     * @var ModelState
     */
    protected $modelState;

    /**
     * Gets or sets action arguments.
     * 
     * @var array
     */
    protected $arguments;

    /** 
     * Gets of sets name of action.
     * 
     * @var string
     */
    protected $actionName;

    /**
     * Gets or sets filters.
     * 
     * @var array
     */
    protected $filters;

    /**
     * Initializes a new instance of the ActionContext for the current request.
     * 
     * @param HttpContextBase|ActionContext $httpContext Context of the request.
     */
    public function __construct($httpContext) {
        if ($httpContext instanceof ActionContext) {
            $this->httpContext = $httpContext->getHttpContext();
            $this->route = $httpContext->getRoute();
            $this->modelState = $httpContext->getModelState();
            $this->filters = $httpContext->getFilters();
            $this->actionName = $httpContext->getActionName();
            $this->arguments = $httpContext->getArguments();
            $this->controller = $httpContext->getController();
        }
        else {
            $this->httpContext = $httpContext;
            $this->route = $httpContext->getRoute();
            $this->modelState = new ModelState();
            $this->filters = array();
        }
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
     * Returns an instance of the controller.
     * 
     * @return Controller
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Returns ModelState.
     * 
     * @return ModelState
     */
    public function getModelState() {
        return $this->modelState;
    }

    /**
     * Returns arguments of the action.
     * 
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Returns name of the action.
     * 
     * @return string
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * Returns list of filters.
     * 
     * @return ActionFilter[]
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Checks the equivalence of the specified string with the name of the action.
     * 
     * @param string $name The string to compare.
     * 
     * @return bool
     */
    public function actionNameEquals($name) {
        return $this->actionName == strtolower($name);
    }
}