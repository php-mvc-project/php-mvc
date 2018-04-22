<?php
namespace PhpMvc;

/**
 * Provides the context for the actionExecuting method of the ActionFilter class.
 */
final class ActionExecutingContext extends ActionContext {

    /**
     * Gets or sets the result that is returned by the action method.
     * 
     * @var ActionResult
     */
    private $result;

    /**
     * Initializes a new instance of the ActionExecutingContext.
     * 
     * @param ActionContext $actionContext The context of the action.
     */
    public function __construct($actionContext) {
        parent::__construct($actionContext);
    }

    /**
     * Gets the result that is returned by the action method.
     * 
     * @return void
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Sets the result that is returned by the action method.
     * 
     * @param mixed $value The result to set.
     * 
     * @return void
     */
    public function setResult($value) {
        $this->result = $value;
    }

}