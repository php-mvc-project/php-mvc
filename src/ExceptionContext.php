<?php
namespace PhpMvc;

/**
 * Provides the context for the exception() method of the ActionFilter class.
 */
final class ExceptionContext extends ControllerContext {

    /**
     * Gets or sets the result that is returned by the action method.
     * 
     * @var ActionResult
     */
    private $result;

    /**
     * Gets or sets the exception that occurred during the execution of the action method, if any.
     * 
     * @var \Exception
     */
    private $exception;

    /**
     * Gets or sets a value that indicates whether the exception has been handled.
     * 
     * @var bool
     */
    private $exceptionHandled;

    /**
     * Initializes a new instance of the ActionExecutedContext.
     * 
     * @param Controller $controller An instance of the controller.
     * @param \Exception $exception The instance of exception.
     */
    public function __construct($controller, $exception) {
        parent::__construct($controller);

        $this->exception = $exception;
        $this->exceptionHandled = false;
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

    /**
     * Gets the exception that occurred during the execution of the action method, if any.
     * 
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }

    /**
     * Sets the exception that occurred during the execution of the action method, if any.
     * 
     * @param \Exception $exception Exception to set.
     * 
     * @return void
     */
    public function setException($exception) {
        $this->exception = $exception;
    }

    /**
     * Gets a value that indicates whether the exception has been handled.
     * 
     * @return bool
     */
    public function getExceptionHandled() {
        return $this->exceptionHandled;
    }

    /**
     * Sets a value that indicates whether the exception has been handled.
     * 
     * @var void
     */
    public function setExceptionHandled($value) {
        $this->exceptionHandled = $value;
    }

}