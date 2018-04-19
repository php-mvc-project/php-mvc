<?php
namespace PhpMvcTest\Filters;

use \PhpMvc\ActionFilter;
use \PhpMvc\ContentResult;

class ExecutingWorldFilter extends ActionFilter {

    /**
     * Called before the action method executes.
     * 
     * @param ActionExecutingContext $actionExecutingContext The action executing context.
     * 
     * @return void
     */
    public function actionExecuting($actionExecutingContext) {
        $actionExecutingContext->setResult(new ContentResult('executing the world!'));
    }

}