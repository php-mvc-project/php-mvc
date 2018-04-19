<?php
namespace PhpMvcTest\Filters;

use \PhpMvc\ActionFilter;
use \PhpMvc\ContentResult;

class NoResultErrorFilter extends ActionFilter {

    /**
     * Called when an exception occurs.
     * 
     * @param ExceptionContext $exceptionContext The context of action exception.
     * 
     * @return void
     */
    public function exception($exceptionContext) {
        $exceptionContext->setExceptionHandled(true);
    }

}