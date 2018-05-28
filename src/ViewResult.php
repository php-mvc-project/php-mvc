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
 * Represents the properties and methods that are needed to render a view.
 */
class ViewResult implements ActionResult {

    /**
     * Gets or sets layout file.
     * 
     * @var string
     */
    public $layout;

    /**
     * Gets or sets view file.
     */
    public $viewFile = null;

    /**
     * Gets or sets page title.
     * 
     * @var string
     */
    public $title;

    /**
     * Gets or sets model.
     * 
     * @var mixed
     */
    public $model;

    /**
     * Gets or sets view data.
     */
    public $viewData = array();

    public function __construct($viewOrModel = null, $model = null, $layout = null) {
        if (gettype($viewOrModel) === 'string') {
            if (($path = PathUtility::getViewFilePath($viewOrModel)) !== false) {
                $this->viewFile = $path;
            }
            else {
                if (empty($model)) {
                    $model = $viewOrModel;
                }
                else {
                    throw new ViewNotFoundException($viewOrModel);
                }
            }
        }
        else {
            if (empty($model)) {
                $model = $viewOrModel;
            }
            else {
                throw new \Exception('The $viewOrModel parameter must contain the name of the view or the model. If the $viewOrModel parameter contains a model, the $model parameter value must be null.');
            }
        }

        $this->model = $model;
        $this->layout = $layout;
    }

    /**
     * Executes the action and outputs the result.
     * 
     * @param ViewContext $viewContext The view context.
     * 
     * @return void
     */
    public function execute($viewContext) {
        !empty($this->title) ? $viewContext->title = $this->title : null;
        !empty($this->viewFile) ? $viewContext->viewFile = PathUtility::getViewFilePath($this->viewFile) : null;
        !empty($this->layout) ? $viewContext->layout = PathUtility::getViewFilePath($this->layout) : null;
        !empty($this->model) ? $viewContext->model = $this->model : null;

        if (!empty($this->viewData)) {
            $viewContext->viewData = array_unique(array_merge($viewContext->viewData, $this->viewData), \SORT_REGULAR);
        }
    }

}