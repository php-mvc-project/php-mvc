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
 * Represents the context of the current view.
 */
final class ViewContext extends ActionContext {

    /**
     * Gets or sets layout file.
     * 
     * @var string
     */
    public $layout;

    /**
     * Gets or sets page title.
     * 
     * @var string
     */
    public $title;

    /**
     * Gets or sets content of the view.
     * 
     * @var string
     */
    public $content;

    /**
     * Gets or sets model.
     * 
     * @var mixed
     */
    public $model;

    /**
     * Gets or sets view data.
     * 
     * @var array
     */
    public $viewData = array();

    /**
     * Gets or sets result of action.
     * 
     * @var mixed
     */
    public $actionResult;

    /**
     * Gets or sets view file name.
     * 
     * @var string
     */
    public $viewFile;

    /**
     * Gets an object that contains the view context information for the parent action method.
     * 
     * @var ViewContext
     */
    public $parentActionViewContext;

    /**
     * Gets or sets parent view context.
     * 
     * @var ViewContext
     */
    public $parent;

    /**
     * Gets or sets body view context.
     * 
     * @var ViewContext
     */
    public $body;

    /**
     * Initializes a new instance of ViewContext.
     * 
     * @param string $viewFile The view file path.
     * @param ActionContext $actionContext The action context of the current request.
     * @param ActionResult $actionResult The result of the action.
     * @param mixed $model The view model.
     * @param array $viewData The view data.
     * @param ViewContext $parentActionViewContext The context of the main view.
     * @param ViewContext $parent The context of the parent.
     * @param ViewContext $body The context of the main child.
     */
    public function __construct($viewFile, $actionContext, $actionResult, $model, $viewData, $parentActionViewContext = null, $parent = null, $body = null) {
        parent::__construct($actionContext->getHttpContext(), $actionContext->getRoute());

        $this->controller = $actionContext->getController();
        $this->modelState = $actionContext->getModelState();
        $this->arguments = $actionContext->getArguments();
        $this->actionName = $actionContext->getActionName();

        $this->viewFile = $viewFile;
        $this->model = $model;
        $this->viewData = $viewData;
        $this->actionResult = $actionResult;
        $this->parentActionViewContext = $parentActionViewContext;
        $this->parent = $parent;
        $this->body = $body;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public function getModelState() {
        return $this->modelState;
    }

    /**
     * Gets the metadata for the specified model key.
     * If there is no data, it returns null.
     * 
     * @param string @key The key to get metadata.
     * 
     * @return ModelDataAnnotation|null
     */
    public function getModelDataAnnotation($key) {
        if (array_key_exists($key, $this->modelState->annotations)) {
            return $this->modelState->annotations[$key];
        }
        else {
            return null;
        }
    }

}