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
 * A base class for an MVC controller.
 */
class Controller {

    /**
     * Gets or sets view data.
     * 
     * @var array
     */
    private $viewData = array();

    /**
     * Instance of ActionContext.
     * 
     * @var ActionContext
     */
    private $actionContext;

    /**
     * Sets data to view.
     * 
     * @param string $key The key associated with the data.
     * @param mixed $value The value to add or update.
     * 
     * @return void
     */
    protected function setData($key, $value) {
        $this->viewData[$key] = $value;
    }

    /**
     * Gets data.
     * 
     * @param string $key The key associated with the data.
     * 
     * @return mixed
     */
    protected function getData($key) {
        return $this->viewData[$key];
    }

    /**
     * Returns current Route.
     * 
     * @return Route
     */
    protected function getRoute() {
        return $this->actionContext->getRoute();
    }

    /**
     * Returns context of the current request.
     * 
     * @return HttpContextBase
     */
    protected function getHttpContext() {
        return $this->actionContext->getHttpContext();
    }

    /**
     * Gets the HttpRequestBase object for the current request.
     * 
     * @return HttpRequestBase
     */
    protected function getRequest() {
        return $this->actionContext->getHttpContext()->getRequest();
    }

    /**
     * Gets the HttpResponseBase object for the current request.
     * 
     * @return HttpResponseBase
     */
    protected function getResponse() {
        return $this->actionContext->getHttpContext()->getResponse();
    }

    /**
     * Gets the HttpSessionProvider object for the current request.
     * 
     * @return HttpSessionProvider
     */
    protected function getSession() {
        return $this->actionContext->getHttpContext()->getSession();
    }

    /**
     * Returns ModelState.
     * 
     * @return ModelState
     */
    protected function getModelState() {
        return $this->actionContext->getModelState();
    }

    /**
     * Creates a ViewResult object that renders a view to the response.
     * 
     * @param string|object|null $viewOrModel View name or model.
     * @param object|null $model Model.
     * @param string|null $layout Layout file name or path.
     * 
     * @return ViewResult
     */
    protected function view($viewOrModel = null, $model = null, $layout = null) {
        return new ViewResult($viewOrModel, $model, $layout);
    }

    /**
     * Creates a JsonResult object that serializes the specified object to JavaScript Object Notation (JSON).
     * 
     * @param mixed $data Data to encode.
     * @param int $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     * JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, 
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, 
     * JSON_PARTIAL_OUTPUT_ON_ERROR. 
     * @param int $depth Set the maximum depth. Must be greater than zero. Default: 512.
     * 
     * @return JsonResult
     */
    protected function json($data, $options = 0, $depth = 512) {
        return new JsonResult($data, $options, $depth);
    }

    /**
     * Creates a FileResult object by using the file contents and file type.
     * 
     * @param string $path The file path to output.
     * @param string $contentType The content type.
     * @param string|bool $downloadName the content-disposition header so that a file-download dialog box is displayed in the browser with the specified file name.
     * 
     * @return FileResult
     */
    protected function file($path, $contentType = null, $downloadName = null) {
        return new FileResult($path, $contentType, $downloadName);
    }

    /**
     * Creates a ContentResult object by using the content and content type.
     * 
     * @param string $content The content to output.
     * @param string $contentType The content type.
     * 
     * @return ContentResult
     */
    protected function content($content, $contentType = 'text/plain') {
        return new ContentResult($content, $contentType);
    }

    /**
     * Creates an HttpStatusCodeResult with the specified status code and description.
     * 
     * @param int $statusCode The HTTP status code.
     * @param string $statusDescription The HTTP status description.
     * 
     * @return HttpStatusCodeResult
     */
    protected function statusCode($statusCode, $statusDescription = null) {
        return new HttpStatusCodeResult($statusCode, $statusDescription);
    }

    /**
     * Creates an HttpStatusCodeResult object with a status code of 404 and the specified description.
     * 
     * @param string $statusDescription The HTTP status description.
     * 
     * @return HttpStatusCodeResult
     */
    protected function notFound($statusDescription = null) {
        return new HttpStatusCodeResult(404, $statusDescription);
    }

    /**
     * Creates an HttpStatusCodeResult object with a status code of 401 and the specified description.
     * 
     * @param string $statusDescription The HTTP status description.
     * 
     * @return HttpStatusCodeResult
     */
    protected function unauthorized($statusDescription = null) {
        return new HttpStatusCodeResult(401, $statusDescription);
    }

    /**
     * Returns TRUE if the request is POST.
     * 
     * @return bool
     */
    protected function isPost() {
        return $this->actionContext->getHttpContext()->getRequest()->isPost();
    }

    /**
     * Creates a RedirectResult object that redirects (HTTP302 - Moved Temporarily) to the specified url.
     * 
     * @param string $url The URL to redirect to.
     * 
     * @return RedirectResult
     */
    protected function redirect($url) {
        return new RedirectResult($url);
    }

    /**
     * Creates a RedirectResult object that redirects (HTTP301 - Moved Permanently) to the specified url.
     * 
     * @param string $url The URL to redirect to.
     * 
     * @return RedirectResult
     */
    protected function redirectPermanent($url) {
        return new RedirectResult($url, true);
    }

    /**
     * Creates a RedirectResult object that redirects (HTTP307 - Temporary Redirect) to the specified url.
     * 
     * @param string $url The URL to redirect to.
     * 
     * @return RedirectResult
     */
    protected function redirectPreserveMethod($url) {
        return new RedirectResult($url, false, true);
    }

    /**
     * Creates a RedirectResult object that redirects (HTTP308 - Permanent Redirect) to the specified url.
     * 
     * @param string $url The URL to redirect to.
     * 
     * @return RedirectResult
     */
    protected function redirectPermanentPreserveMethod($url) {
        return new RedirectResult($url, true, true);
    }

    /**
     * Redirects (HTTP302 - Moved Temporarily) to the specified action, 
     * using the specified actionName, controllerName, routeValues, and fragment.
     * 
     * @param string $actionName The name of the action to use for generating the URL.
     * @param string $controllerName The name of the controller to use for generating the URL.
     * @param array $routeValues The route data to use for generating the URL.
     * @param string $fragment The fragment to add to the URL.
     * 
     * @return RedirectToActionResult
     */
    protected function redirectToAction($actionName, $controllerName = null, $routeValues = null, $fragment = null) {
        return new RedirectToActionResult($actionName, $controllerName, $routeValues, $fragment);
    }

}