<?php
namespace PhpMvc;

/**
 * Represents a result containing an exception.
 */
class ExceptionResult implements ActionResult {

    /**
     * Gets or sets exception instance.
     * 
     * @var \Exception
     */
    private $exception;

    /**
     * Initializes a new instance of ExceptionResult.
     * 
     * @param \Exception $exception The exception instance.
     */
    public function __construct($exception) {
        $this->exception = $exception;
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
    }

    /**
     * Returns exception instance.
     * 
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }

}