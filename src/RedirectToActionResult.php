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
 * An ActionResult that returns a Found (302), 
 * Moved Permanently (301), Temporary Redirect (307), 
 * or Permanent Redirect (308) response with a Location header. 
 * Targets a controller action.
 */
class RedirectToActionResult extends RedirectResult {

    /**
     * Gets or sets the name of the action to use for generating the URL.
     * 
     * @var string
     */
    private $actionName;

    /**
     * Gets or sets the name of the controller to use for generating the URL.
     * 
     * @var string
     */
    private $controllerName;

    /**
     * Gets or sets the route data to use for generating the URL.
     * 
     * @var array
     */
    private $routeValues;

    /**
     * Gets or sets the fragment to add to the URL.
     * 
     * @var string
     */
    private $fragment;

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
     * Initializes a new instance of RedirectToActionResult.
     * 
     * @param string $actionName The name of the action to use for generating the URL.
     * @param string $controllerName The name of the controller to use for generating the URL.
     * @param array $routeValues The route data to use for generating the URL.
     * @param string $fragment The fragment to add to the URL.
     * @param bool $permanent A value that indicates whether the redirection should be permanent.
     * @param bool $preserveMethod If set to true, make the temporary redirect (307) or permanent redirect (308) preserve the intial request method.
     */
    public function __construct($actionName, $controllerName = null, $routeValues = null, $fragment = null, $permanent = false, $preserveMethod = false) {
        parent::__construct(null, $permanent, $preserveMethod);

        $this->actionName = !empty($actionName) ? $actionName : PHPMVC_ACTION;
        $this->controllerName = !empty($controllerName) ? $controllerName : PHPMVC_VIEW;
        $this->routeValues = $routeValues;
        $this->fragment = $fragment;
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
        $this->url = UrlHelper::action($actionContext, $this->actionName, $this->controllerName, $this->routeValues, $this->fragment);
        parent::execute($actionContext);
    }

}