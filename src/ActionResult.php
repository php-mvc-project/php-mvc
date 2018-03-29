<?php
namespace PhpMvc;

/**
 * Defines a contract that represents the result of an action method.
 */
interface ActionResult {

    /**
     * Executes the action and outputs the result.
     * 
     * @param ActionContext $actionContext The context in which the result is executed.
     * The context information includes information about the action that was executed and request information.
     * 
     * @return void
     */
    function execute($actionContext);

}