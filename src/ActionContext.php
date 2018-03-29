<?php
namespace PhpMvc;

/**
 * Context object for execution of action which has been selected as part of an HTTP request.
 */
class ActionContext {

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

}