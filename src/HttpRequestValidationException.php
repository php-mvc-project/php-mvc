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
 * The exception that is thrown when a potentially malicious input string is received from the client as part of the request data.
 */
final class HttpRequestValidationException extends \Exception {

    /**
     * Initializes a new instance of the HttpRequestValidationException.
     * 
     */
    public function __construct($source = null, $key = null, $value = null) {
        $specific = '';

        if (!empty($source) && !empty($key) && !empty($value)) {
            $specific = 'A potentially dangerous ' . $source . ' value was obtained from the client (' . $key . ' = "' . $value . '").' . chr(10) . chr(10);
        }

        parent::__construct(
            $specific . 
            'Request validation detected a potentially dangerous input value from the client and aborted the request. ' .
            'This might be an attemp of using cross-site scripting to compromise the security of your site. ' .
            'You can disable request validation using the code AppBuilder::useValidation(array(\'crossSiteScripting\' => false)).'
        );
    }

}