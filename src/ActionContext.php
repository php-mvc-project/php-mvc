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
 * Context object for execution of action which has been selected as part of an HTTP request.
 */
class ActionContext {

    /**
     * Gets or sets context of the current request.
     * 
     * @var HttpContextBase
     */
    protected $httpContext;

    /**
     * Gets or sets the Route for the current request.
     * 
     * @var Route
     */
    protected $route;

    /**
     * Gets or sets the controller.
     * 
     * @var Controller
     */
    protected $controller;

    /**
     * Gets or sets the state of model.
     * 
     * @var ModelState
     */
    protected $modelState;

    /**
     * Gets or sets action arguments.
     * 
     * @var array
     */
    protected $arguments;

    /** 
     * Gets of sets name of action.
     * 
     * @var string
     */
    protected $actionName;

    /**
     * Gets or sets filters.
     * 
     * @var array
     */
    protected $filters;

    /**
     * Gets or sets output cache settings.
     * 
     * @var array;
     */
    protected $outputCache;

    /**
     * Gets or sets cache entry key.
     * 
     * @var string
     */
    protected $cacheKey;

    /**
     * Initializes a new instance of the ActionContext for the current request.
     * 
     * @param HttpContextBase|ActionContext $httpContext Context of the request.
     */
    public function __construct($httpContext) {
        if ($httpContext instanceof ActionContext) {
            $this->httpContext = $httpContext->getHttpContext();
            $this->route = $httpContext->getRoute();
            $this->modelState = $httpContext->getModelState();
            $this->filters = $httpContext->getFilters();
            $this->actionName = $httpContext->getActionName();
            $this->arguments = $httpContext->getArguments();
            $this->controller = $httpContext->getController();
            $this->outputCache = $httpContext->getOutputCacheSettings();
        }
        else {
            $this->httpContext = $httpContext;
            $this->route = $httpContext->getRoute();
            $this->modelState = new ModelState();
            $this->filters = array();
            $this->outputCache = array();
            $this->arguments = array();
        }
    }

    /**
     * Returns context of the current request.
     * 
     * @return HttpContextBase
     */
    public function getHttpContext() {
        return $this->httpContext;
    }

    /**
     * Returns current Route.
     * 
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Returns an instance of the controller.
     * 
     * @return Controller
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Returns ModelState.
     * 
     * @return ModelState
     */
    public function getModelState() {
        return $this->modelState;
    }

    /**
     * Returns arguments of the action.
     * 
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Returns name of the action.
     * 
     * @return string
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * Checks the equivalence of the specified string with the name of the action.
     * 
     * @param string|array $name The string or string array to compare.
     * 
     * @return bool
     */
    public function actionNameEquals($name) {
        $actionName = strtolower($this->actionName);

        if (is_array($name)) {
            foreach ($name as $n) {
                if ($actionName == strtolower($n)) {
                    return true;
                }
            }

            return false;
        }
        else {
            return $actionName == strtolower($name);
        }
    }

    /**
     * Returns name of the controller.
     * 
     * @return string
     */
    public function getControllerName() {
        return $this->getRoute()->values['controller'];
    }

    /**
     * Checks the equivalence of the specified string with the name of the controller.
     * 
     * @param string|array $name The string or string array to compare.
     * 
     * @return bool
     */
    public function controllerNameEquals($name) {
        $controllerName = strtolower($this->getControllerName());

        if (is_array($name)) {
            foreach ($name as $n) {
                if ($controllerName == strtolower($n)) {
                    return true;
                }
            }

            return false;
        }
        else {
            return $controllerName == strtolower($name);
        }
    }

    /**
     * Returns list of filters.
     * 
     * @return ActionFilter[]
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * Returns output cache settings.
     * 
     * @return array
     */
    public function getOutputCacheSettings() {
        return $this->outputCache;
    }

}