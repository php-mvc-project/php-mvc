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
 * Represents a user-defined content type that is the result of an action method.
 */
class ContentResult implements ActionResult {

    /**
     * Gets or sets the content.
     * 
     * @var mixed
     */
    private $content;

    /**
     * Gets or sets the type of the content. For example: text/plain, text/html.
     * 
     * @var string
     */
    private $contentType;
   
    /**
     * Initializes a new instance of ContentResult.
     * 
     * @param string $content The content.
     * @param string $contentType The content type. Default: text/plain.
     */
    public function __construct($content, $contentType = 'text/plain') {
        $this->content = $content;
        $this->contentType = $contentType;
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
        $response = $actionContext->getHttpContext()->getResponse();
        $response->addHeader('Content-Type', (!empty($this->contentType) ? $this->contentType : 'text/plain'));
        $response->write($this->content);
        $response->end();
    }

}