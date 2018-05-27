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
 * Controls the processing of application actions by redirecting to a specified URI.
 */
class RedirectResult implements ActionResult {

    /**
     * Gets or sets the target URL.
     * 
     * @var string
     */
    protected $url;

    /**
     * Gets or sets a value that indicates whether the redirection should be permanent.
     * 
     * @var bool
     */
    private $permanent;

    /**
     * Gets or sets an indication that the redirect preserves the initial request method.
     * 
     * @var bool
     */
    private $preserveMethod;

    /**
     * Initializes a new instance of RedirectResult.
     * 
     * @param int $url The target URL.
     * @param bool $permanent A value that indicates whether the redirection should be permanent.
     * @param bool $preserveMethod If set to true, make the temporary redirect (307) or permanent redirect (308) preserve the intial request method.
     */
    public function __construct($url, $permanent = false, $preserveMethod = false) {
        $this->url = $url;
        $this->permanent = $permanent;
        $this->preserveMethod = $preserveMethod;
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

        if ($this->permanent === true && $this->preserveMethod !== true) {
            $response->setStatusCode(301);
        }
        elseif ($this->permanent !== true && $this->preserveMethod === true) {
            $response->setStatusCode(307);
        }
        elseif ($this->permanent === true && $this->preserveMethod === true) {
            $response->setStatusCode(308);
        }

        $response->addHeader('Location', $this->url);

        $response->end();
    }

}