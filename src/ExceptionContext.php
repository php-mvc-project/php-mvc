<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpMvc;

/**
 * Provides the context for the exception() method of the ActionFilter class.
 */
final class ExceptionContext extends ActionContext {

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