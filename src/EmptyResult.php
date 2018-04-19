<?php
namespace PhpMvc;

/**
 * Represents a result that does nothing.
 */
class EmptyResult implements ActionResult {

    /**
     * Initializes a new instance of EmptyResult.
     */
    public function __construct() {
    }

    /**
     * Executes the action and outputs the result.
     * 
     * @param ActionContext $actionContext The context in which the result is executed.
     * The context information includes information about the action that was executed and request information.
     * 
     * @return void
     */
    public function execute($actionContext) {
        $actionContext->getHttpContext()->getResponse()->end();
    }

}