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

require_once 'InternalHelper.php';
require_once 'UrlParameter.php';
require_once 'RouteOptions.php';
require_once 'RouteCollection.php';
require_once 'RouteSegment.php';
require_once 'Route.php';
require_once 'RouteProvider.php';
require_once 'DefaultRouteProvider.php';
require_once 'CacheProvider.php';
require_once 'IdleCacheProvider.php';
require_once 'HttpContextBase.php';
require_once 'HttpRequestBase.php';
require_once 'HttpResponseBase.php';
require_once 'HttpSessionProvider.php';
require_once 'HttpContextInfo.php';
require_once 'HttpContext.php';
require_once 'HttpRequest.php';
require_once 'HttpResponse.php';
require_once 'HttpSession.php';
require_once 'EventArgs.php';
require_once 'ErrorHandlerEventArgs.php';
require_once 'Model.php';
require_once 'Filter.php';
require_once 'OutputCacheLocation.php';
require_once 'OutputCache.php';
require_once 'ValidateAntiForgeryToken.php';
require_once 'ModelState.php';
require_once 'ModelStateEntry.php';
require_once 'AppContext.php';
require_once 'ActionContext.php';
require_once 'ActionResult.php';
require_once 'ViewResult.php';
require_once 'ViewContext.php';
require_once 'PathUtility.php';
require_once 'View.php';
require_once 'Html.php';
require_once 'ActionExecutingContext.php';
require_once 'ActionExecutedContext.php';
require_once 'AppBuilder.php';