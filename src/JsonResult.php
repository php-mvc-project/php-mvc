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
 * Represents a class that is used to send JSON-formatted content to the response.
 */
class JsonResult implements ActionResult {

    /**
     * Gets or sets the data.
     * 
     * @var mixed
     */
    public $data;

    /**
     * Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     * JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, 
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, 
     * JSON_PARTIAL_OUTPUT_ON_ERROR. 
     */
    public $options;

    /** 
     * Gets or sets the maximum depth. Must be greater than zero.
     */
    public $depth;

    /**
     * Initializes a new instance of JsonResult.
     * 
     * @param mixed $data Data to encode.
     * @param int $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     * JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, 
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, 
     * JSON_PARTIAL_OUTPUT_ON_ERROR. 
     * @param int $depth Set the maximum depth. Must be greater than zero. Default: 512.
     */
    public function __construct($data, $options = 0, $depth = 512) {
        $this->data = $data;
        $this->options = $options;
        $this->depth = $depth;
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
        // make json and exit
        if (($result = json_encode($this->data, $this->options, $this->depth)) === false) {
            throw new \ErrorException('JSON encode error #' . json_last_error() . ': ' . json_last_error_msg());
        }

        $response = $actionContext->getHttpContext()->getResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->write($result);
        $response->end();
    }

}