<?php
namespace PhpMvcTest\Filters;

use \PhpMvc\ActionFilter;
use \PhpMvc\ContentResult;

class ExecutedWorldFilter extends ActionFilter {

    /**
     * Called after the action method executes.
     * 
     * @param ActionExecutedContext $actionExecutedContext The context of the action executed.
     * 
     * @return void
     */
    public function actionExecuted($actionExecutedContext) {
        if (($ex = $actionExecutedContext->getException()) === null) {
            $actionExecutedContext->setResult(new ContentResult('executed the world!'));
        }
        else {
            $actionExecutedContext->setResult(new ContentResult($ex->getMessage()));
        }
    }

}