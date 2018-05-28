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
final class View {

    /**
     * Defines ViewContext.
     * 
     * @var ViewContext
     */
    private static $viewContext;

    /**
     * Gets the context of the current view.
     * 
     * @return ViewContext
     */
    public static function getViewContext() {
        return self::$viewContext;
    }

    /**
     * Gets context of the current request.
     * 
     * @return HttpContextBase
     */
    public static function getHttpContext() {
        return self::$viewContext->getHttpContext();
    }

    /**
     * Gets route of the current request.
     * 
     * @return Route
     */
    public static function getRoute() {
        return self::$viewContext->getHttpContext()->getRoute();
    }

    /**
     * Gets the HttpRequestBase object for the current request.
     * 
     * @return HttpRequestBase
     */
    public static function getRequest() {
        return self::$viewContext->getHttpContext()->getRequest();
    }

    /**
     * Gets the HttpResponseBase object for the current request.
     * 
     * @return HttpResponseBase
     */
    public static function getResponse() {
        return self::$viewContext->getHttpContext()->getResponse();
    }

    /**
     * Sets layout.
     * 
     * @param string $path The layout file name in the shared folder or full path to layout file.
     * 
     * @return void
     */
    public static function setLayout($path) {
        self::$viewContext->layout = $path;
    }

    /**
     * Gets current layout file name or path.
     * 
     * @return void
     */
    public static function getLayout() {
        return self::$viewContext->layout;
    }

    /**
     * Sets page title.
     * 
     * @param string $title The title to set.
     * 
     * @return void
     */
    public static function setTitle($title) {
        self::$viewContext->title = $title;
    }

    /**
     * Gets page title.
     * 
     * @return void
     */
    public static function getTitle() {
        return self::$viewContext->title;
    }

    /**
     * Injects model to state.
     * 
     * @param mixed &$model Model to injection.
     * 
     * @return void
     */
    public static function injectModel(&$model) {
        $actionResult = self::$viewContext->actionResult;

        if (!empty($actionResult)) {
            if ($actionResult instanceof ViewResult && !empty($actionResult->model)) {
                $model = $actionResult->model;
            }
            else {
                $model = null;
            }
        }
    }

    /**
     * Sets data to view.
     * 
     * @param string $key Key associated with the data entry.
     * @param string $value The value to set.
     * 
     * @return void
     */
    public static function setData($key, $value) {
        self::$viewContext->viewData[$key] = $value;
    }

    /**
     * Gets the data with the specified key.
     * If the specified key does not exist, function returns null.
     * If no key is specified, returns all data.
     * 
     * @param string $key The key to get the data.
     * 
     * @return mixed|array|null
     */
    public static function getData($key = null) {
        if (!isset($key)) {
            return self::$viewContext->viewData;
        }
        else {
            return isset(self::$viewContext->viewData[$key]) ? self::$viewContext->viewData[$key] : null;
        }
    }

    /**
     * Gets model.
     * 
     * @return mixed|null
     */
    public static function getModel() {
        return self::$viewContext->model;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public static function getModelState() {
        return self::$viewContext->getModelState();
    }

    /**
     * Gets view file name.
     * 
     * @return string
     */
    public static function getViewFile() {
        return self::$viewContext->viewFile;
    }

}