<?php
namespace PhpMvc;

/**
 * Represents the arguments to the error event handler.
 */
final class ErrorHandlerEventArgs extends EventArgs {

    /**
     * Exception instance.
     * 
     * @var \Exception
     */
    private $exception;

    /**
     * Indicates that the processing is done and you do not need to run other handlers, including the standard handler.
     * 
     * @var bool
     */
    private $handled = false;

    /**
     * Initializes a new instance of ErrorHandlerEventArgs.
     * 
     * @param \Exception $exception The exception instance.
     */
    public function __construct($exception) {
        $this->exception = $exception;
    }

    /**
     * Gets exception.
     * 
     * @return \Exception
     */
    public function getException() {
        return $this->exception;
    }

    /**
     * Gets exception message.
     * 
     * @return string
     */
    public function getMessage() {
        return $this->exception->getMessage();
    }

    /**
     * Gets handled status.
     * 
     * @return bool
     */
    public function getHandled() {
        return $this->handled;
    }

    /**
     * Sets handled status.
     * 
     * @param bool $value
     * 
     * @return void
     */
    public function setHandled($value) {
        $this->handled = $value;
    }

}