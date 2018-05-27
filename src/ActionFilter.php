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