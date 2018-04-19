<?php
namespace PhpMvc;

/**
 * Represents the base class for action filters.
 */
abstract class ActionFilter {

    /**
     * Called before the action method executes.
     * 
     * @param ActionExecutingContext $actionExecutingContext The context of the executing action.
     * 
     * @return void
     */
    public function actionExecuting($actionExecutingContext) {

    }

    /**
     * Called after the action method executes.
     * 
     * @param ActionExecutedContext $actionExecutedContext The context of the action executed.
     * 
     * @return void
     */
    public function actionExecuted($actionExecutedContext) {

    }

    /**
     * Called when an exception occurs.
     * 
     * @param ExceptionContext $exceptionContext The context of action exception.
     * 
     * @return void
     */
    public function exception($exceptionContext) {

    }

}