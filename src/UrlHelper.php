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
 * Helper class for building URI.
 */
final class UrlHelper {

    /**
     * Generates a URL with an absolute path for an action method, which contains the
     * action name, controller name, route values, protocol to use, host name, and fragment.
     * Generates an absolute URL if $schema and $host are non-null.
     * 
     * @param ActionContext $actionContext The current context of the action.
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * 
     * @return string
     */
    public static function action($actionContext, $actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        if (empty($actionContext) || !$actionContext instanceof ActionContext) {
            throw new \Exception('The $actionContext is requred and type must be derived from "\PhpMvc\ActionContext".');
        }

        if (empty($actionName)) {
            $modelState->addError($key, '$actionName is required. Value must not be empty.');
        }

        return $actionContext->getHttpContext()->tryBuildUrl($actionName, $controllerName, $routeValues, $fragment, $schema, $host);
    }

}