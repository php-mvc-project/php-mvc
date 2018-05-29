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
 * The main class that works wonders.
 */
final class AppBuilder {

    /**
     * @var AppContext
     */
    private static $appContext;

    /**
     * The config of the AppBuilder.
     * 
     * @var array
     */
    private static $config = array();

    /**
     * Sets the application namespace.
     * 
     * @param string $appNamespace The root namespace of the application.
     * 
     * @return void
     */
    public static function useNamespace($appNamespace) {
        self::$config['appNamespace'] = $appNamespace;
    }

    /**
     * Sets base path.
     * 
     * @param string $basePath The root path of the application.
     * 
     * @return void
     */
    public static function useBasePath($basePath) {
        self::$config['basePath'] = $basePath;
    }

    /**
     * Sets context of the HTTP request.
     * 
     * @param HttpContextBase $httpContext The context to set.
     * 
     * @return void
     */
    public static function useHttpContext($httpContext) {
        self::$config['httpContext'] = $httpContext;
    }

    /**
     * Sets cache provider.
     * 
     * @param CacheProvider $cacheProvider The cache provider to set.
     * 
     * @return void
     */
    public static function useCache($cacheProvider) {
        self::$config['cacheProvider'] = $cacheProvider;
    }

    /**
     * Sets routе provider.
     * 
     * @param RouteProvider $routeProvider The route provider to set.
     * 
     * @return void
     */
    public static function useRouter($routeProvider) {
        self::$config['routeProvider'] = $routeProvider;
    }

    /**
     * Sets response HTTP headers.
     * 
     * @param array $headers The HTTP headers collection.
     * For example: array('X-Powered-By' => 'PHP', 'X-PhpVersion' => '7.0')
     * 
     * @return void
     */
    public static function useHeaders($headers) {
        self::$config['httpHeaders'] = $headers;
    }

    /**
     * Start new or resume existing session.
     * 
     * @param HttpSessionProvider $sessionProvider The session provider to set.
     * 
     * @return void
     */
    public static function useSession($sessionProvider = null) {
        self::$config['sessionProvider'] = (isset($sessionProvider) ? $sessionProvider : true);
    }

    /**
     * Allows to manage HTTP request validators.
     * By default, all validators are enabled. With this method, you can disable certain validators.
     * 
     * WARNING: Disabling HTTP requests validation can jeopardize your site and users of your site.
     * For security reasons, it is not recommended to disable HTTP request validation.
     * 
     * @param array|bool $validators List of validators to use or disable.
     * For example:
     * array(
     *  'crossSiteScripting' => false, // disable CrossSiteScriptingValidation
     *  'actionName' => false // disable action name validation
     * )
     * 
     * @return void
     */
    public static function useValidation($validators) {
        self::$config['validators'] = $validators;
    }

    /**
     * Sets custom handlers.
     * 
     * @param callback $callback
     * For example:
     * AppBuilder::useAppContext(function(\PhpMvc\AppContext $appContext) {
     *   // ...
     * });
     * 
     * @return void
     */
    public static function useAppContext($callback) {
        self::$config['customHandlers'] = $callback;
    }

    /**
     * Adds a custom setting.
     * 
     * @param string $key The unique key.
     * @param mixed $value The value.
     * 
     * @return void
     */
    public static function set($key, $value) {
        if (!isset(self::$config['settings'])) {
            self::$config['settings'] = array();
        }

        self::$config['settings'][$key] = $value;
    }

    /**
     * Gets a custom setting.
     * 
     * @param string $key The name of the parameter to get.
     * 
     * @return mixed
     */
    public static function get($key) {
        if (!isset(self::$config['settings']) || !isset(self::$config['settings'][$key])) {
            return null;
        }

        return self::$config['settings'][$key];
    }

    /**
     * Registers routes.
     * 
     * @param callback $routes A function in which an instance of the route provider will be passed, through which routing rules are created.
     * For example:
     * AppBuilder::routes(function($routes: RouteProvider) {
     *   $routes->ignore('content/{*file}');
     *   $routes->add('default', '{controller=Home}/{action=index}/{id?}');
     * });
     * 
     * @returnv void
     */
    public static function routes($routes) {
        self::$config['routes'] = $routes;
    }

    /**
     * Processes the current HTTP request.
     * 
     * @return void
     */
    public static function build() {
        try {
            self::init();
            self::dependencies();
    
            $route = self::canRoute();
    
            if ($route !== false) {
                $actionContext = self::actionContext($route);
    
                if ($actionContext !== null && !self::cached($actionContext)) {
                    self::filters($actionContext);
                    self::headers();
                    self::validation();
                    self::render($actionContext);
                }
            }
        }
        catch (\Exception $ex) {
            $errorHandlerEventArgs = new ErrorHandlerEventArgs($ex);
            self::invokeAll(self::$appContext->getErrorHandler(), array($errorHandlerEventArgs));

            if ($errorHandlerEventArgs->getHandled() !== true) {
                throw $ex;
            }
        }
    }

    /**
     * Adds headers.
     * 
     * @return void
     */
    private static function headers() {
        if (empty(self::$config['httpHeaders'])) {
            return;
        }

        $response = self::$config['httpContext']->getResponse();
        $response->setHeaders(self::$config['httpHeaders']);
    }

    /**
     * Performs the initialization of the builder.
     * 
     * @return void
     */
    private static function init() {
        self::$appContext = new AppContext(self::$config);

        if (isset(self::$config['customHandlers'])) {
            if (is_callable(self::$config['customHandlers'])) {
                call_user_func(self::$config['customHandlers'], self::$appContext);
            }
            else {
                throw new \Exception('Function is expected.');
            }
        }

        self::invokeAll(self::$appContext->getPreInit(), array(self::$appContext));

        $config = self::$appContext->getConfig();

        if (empty($config['appNamespace'])) {
            throw new \Exception('The root namespace for the application is required. To specify the namespace of your application, use the useNamespace method. The value must not be null or empty.');
        }

        if (empty($config['basePath'])) {
            $config['basePath'] = getcwd();
        }

        if (!defined('PHPMVC_DS')) { define('PHPMVC_DS', DIRECTORY_SEPARATOR); }
        if (!defined('PHPMVC_ROOT_PATH')) { define('PHPMVC_ROOT_PATH', $config['basePath'] . PHPMVC_DS); }
        if (!defined('PHPMVC_CORE_PATH')) { define('PHPMVC_CORE_PATH', __DIR__ .PHPMVC_DS); }
        if (!defined('PHPMVC_CONFIG_PATH')) { define('PHPMVC_CONFIG_PATH', PHPMVC_ROOT_PATH . 'config' . PHPMVC_DS); }
        if (!defined('PHPMVC_FILTER_PATH')) { define('PHPMVC_FILTER_PATH', PHPMVC_ROOT_PATH . 'filters' . PHPMVC_DS); }
        if (!defined('PHPMVC_CONTROLLER_PATH')) { define('PHPMVC_CONTROLLER_PATH', PHPMVC_ROOT_PATH . 'controllers' . PHPMVC_DS); }
        if (!defined('PHPMVC_MODEL_PATH')) { define('PHPMVC_MODEL_PATH', PHPMVC_ROOT_PATH . 'models' . PHPMVC_DS); }
        if (!defined('PHPMVC_VIEW_PATH')) { define('PHPMVC_VIEW_PATH', PHPMVC_ROOT_PATH . 'views' . PHPMVC_DS); }
        if (!defined('PHPMVC_SHARED_PATH')) { define('PHPMVC_SHARED_PATH', PHPMVC_VIEW_PATH . 'shared' . PHPMVC_DS); }
        if (!defined('PHPMVC_UPLOAD_PATH')) { define('PHPMVC_UPLOAD_PATH', PHPMVC_ROOT_PATH . 'upload' . PHPMVC_DS); }

        if (!defined('PHPMVC_APP_NAMESPACE')) {
            define('PHPMVC_APP_NAMESPACE', $config['appNamespace']);
        }
        elseif (PHPMVC_APP_NAMESPACE !== $config['appNamespace']) {
            throw new \Exception('Constant PHPMVC_CONTROLLER already defined. Re-define with other value is not possible.');
        }

        if (empty($config['routeProvider'])) {
            $config['routeProvider'] = new DefaultRouteProvider();
        }
        elseif (!$config['routeProvider'] instanceof RouteProvider) {
            throw new \Exception('The routeProvider type must be the base of "\PhpMvc\RouteProvider".');
        }

        $config['routeProvider']->init();

        if (isset($config['routes'])) {
            if (is_callable($config['routes'])) {
                $config['routes']($config['routeProvider']);
            }
            elseif (is_array($config['routes'])) {
                $provider = $config['routeProvider'];

                foreach($config['routes'][0] as $route) {
                    $provider->add($route->name, $route->template, $route->defaults, $route->constraints);
                }

                if (count($config['routes']) > 1) {
                    foreach($config['routes'][1] as $route) {
                        $provider->ingnore($route->template, $route->constraints);
                    }
                }
            }
            elseif ($config['routes'] instanceof RouteCollection) {
                $provider = $config['routeProvider'];

                foreach($config['routes'] as $route) {
                    $provider->add($route->name, $route->template, $route->defaults, $route->constraints);
                }
            }
        }

        if (empty($config['cacheProvider'])) {
            $config['cacheProvider'] = new IdleCacheProvider();
        }
        elseif (!$config['cacheProvider'] instanceof CacheProvider) {
            throw new \Exception('The $cacheProvider type must be the base of "\PhpMvc\CacheProvider".');
        }

        $config['cacheProvider']->init();

        if (isset($config['sessionProvider'])) {
            if ($config['sessionProvider'] === true) {
                $config['sessionProvider'] = new HttpSession();
            }
            elseif (!$config['sessionProvider'] instanceof HttpSessionProvider) {
                throw new \Exception('The $sessionProvider type must be the base of "\PhpMvc\HttpSessionProvider".');
            }

            $config['sessionProvider']->init();
        }

        if (empty($config['httpContext'])) {
            $info = new HttpContextInfo();
            $info->routeProvider = $config['routeProvider'];
            $info->cacheProvider = $config['cacheProvider'];
            $info->request = new HttpRequest();
            $info->response = new HttpResponse();
            $info->session = isset($config['sessionProvider']) ? $config['sessionProvider'] : null;

            $config['httpContext'] = new HttpContext($info);
        }
        elseif (!$config['httpContext'] instanceof HttpContextBase) {
            throw new \Exception('The httpContext type must be the base of "\PhpMvc\HttpContextBase".');
        }

        // default response handlers
        $response = $config['httpContext']->getResponse();

        $response->setEndHandler(function() use ($config) {
            self::invokeAll(self::$appContext->getEnd(), array(new ActionContext($config['httpContext'])));
        });

        $response->setPreSendHandler(function() use ($config) {
            self::invokeAll(self::$appContext->getPreSend(), array(new ActionContext($config['httpContext'])));
        });

        // set context and config, and invoke init handlers
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\HttpContext', 'current', $config['httpContext']);

        self::$appContext->setConfig($config);
        self::invokeAll(self::$appContext->getInit(), array(self::$appContext));
        self::$config = self::$appContext->getConfig();
    }

    /**
     * Looks for a suitable route and checks whether the request can be executed.
     * 
     * @return Route|bool
     */
    private static function canRoute() {
        $httpContext = self::$config['httpContext'];

        if ($httpContext->isIgnoredRoute()) {
            $request = $httpContext->getRequest();
            $response = $httpContext->getResponse();
            $response->clear();

            if (!file_exists($request->documentRoot() . $request->path())) {
                $response->setStatusCode(404);
            }
            else {
                require($request->documentRoot() . $request->path());
            }

            $response->end();
            return false;
        }

        $route = $httpContext->getRoute();

        if ($route == null) {
            $response = $httpContext->getResponse();
            $response->setStatusCode(404);
            $response->end();
            return false;
        }

        return $route;
    }

    /**
     * Initializes the ActionContext associated with the current request and route.
     * 
     * @param Route $route The route of the request.
     * 
     * @return ActionContext
     */
    private static function actionContext($route) {
        $httpContext = self::$config['httpContext'];
        $response = $httpContext->getResponse();

        // TODO: kill constants
        if (!defined('PHPMVC_CONTROLLER')) { define('PHPMVC_CONTROLLER', ucfirst($route->getValueOrDefault('controller', 'Home'))); }
        if (!defined('PHPMVC_ACTION')) { define('PHPMVC_ACTION', $route->getValueOrDefault('action', 'index')); }
        if (!defined('PHPMVC_VIEW')) { define('PHPMVC_VIEW', strtolower(PHPMVC_CONTROLLER)); }
        if (!defined('PHPMVC_CURRENT_CONTROLLER_PATH')) { define('PHPMVC_CURRENT_CONTROLLER_PATH', PHPMVC_CONTROLLER_PATH . PHPMVC_VIEW . PHPMVC_DS); }
        if (!defined('PHPMVC_CURRENT_VIEW_PATH')) { define('PHPMVC_CURRENT_VIEW_PATH', PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . PHPMVC_ACTION . '.php'); }

        $controllerClassName = '\\' . PHPMVC_APP_NAMESPACE . '\\Controllers\\' . PHPMVC_CONTROLLER . 'Controller';

        if (!class_exists($controllerClassName)) {
            $response->setStatusCode(404);
            $response->end();
            return null;
        }

        // create action context
        $actionContext = new ActionContext($httpContext);
        InternalHelper::setPropertyValue($actionContext, 'actionName', PHPMVC_ACTION);

        // preparing to create an instance of the controller class
        $controllerClass = new \ReflectionClass($controllerClassName);
        
        if (!$controllerClass->isSubclassOf('\\PhpMvc\\Controller')) {
            throw new \Exception('The controller type must be the base of "\\PhpMvc\\Controller".');
        }

        $actionContextProperty = $controllerClass->getParentClass()->getProperty('actionContext');
        $actionContextProperty->setAccessible(true);

        // set action context
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Model', 'actionContext', $actionContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Filter', 'actionContext', $actionContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\OutputCache', 'actionContext', $actionContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\ValidateAntiForgeryToken', 'actionContext', $actionContext);

        // create instance of controller
        if ($controllerClass->getConstructor() != null) {
            $controllerInstance = $controllerClass->newInstance();
        }
        else {
            $controllerInstance = $controllerClass->newInstanceWithoutConstructor();
        }

        $actionContextProperty->setValue($controllerInstance, $actionContext);

        InternalHelper::setPropertyValue($actionContext, 'controller', $controllerInstance);

        if (!method_exists($controllerInstance, PHPMVC_ACTION)) {
            $response->setStatusCode(404);
            $response->end();
            return null;
        }

        // get and set model annotations
        $annotations = InternalHelper::getStaticPropertyValue('\\PhpMvc\\Model', 'annotations');
        $modelState = $actionContext->getModelState();
        $modelState->annotations = $annotations;
        // InternalHelper::setPropertyValue($modelState, 'annotations', $annotations);

        // response handling
        $response->setFlushHandler(function($eventArgs) use ($actionContext) {
            self::cachingPartial($actionContext, $eventArgs);
            self::invokeAll(self::$appContext->getFlush(), array($actionContext, $eventArgs));
        });

        $response->setEndHandler(function() use ($actionContext) {
            self::caching($actionContext);
            self::invokeAll(self::$appContext->getEnd(), array($actionContext));
        });

        $response->setPreSendHandler(function() use ($actionContext) {
            self::cachingClient($actionContext);
            self::invokeAll(self::$appContext->getPreSend(), array($actionContext));
        });

        // arguments and model state
        self::makeActionState($actionContext);

        // invoke custom handlers
        self::invokeAll(self::$appContext->getActionContextInit(), array($actionContext));

        return $actionContext;
    }

    /**
     * Checks the cache and outputs the result if there is data in the cache for the current request.
     * 
     * @param ActionContext $actionContext The action context.
     * 
     * @return bool
     */
    private static function cached($actionContext) {
        $httpContext = $actionContext->getHttpContext();
        $request = $httpContext->getRequest();

        // TODO: support POST
        if ($request->isPost()) {
            return false;
        }

        $cacheSettings = InternalHelper::getStaticPropertyValue('\\PhpMvc\\OutputCache', 'settings');
        $actionName = $actionContext->getActionName();

        if (!isset($cacheSettings[$actionName])) {
            $cacheSettings[$actionName] = array();
        }

        if (!isset($cacheSettings['.'])) {
            $cacheSettings['.'] = array();
        }

        if (empty($cacheSettings[$actionName]) && empty($cacheSettings['.'])) {
            return false;
        }

        // location
        if (!empty($cacheSettings[$actionName]['location'])) {
            $location = intval($cacheSettings[$actionName]['location']);
        }
        elseif (!empty($cacheSettings['.']['location'])) {
            $location = intval($cacheSettings['.']['location']);
        }
        else {
            $location = OutputCacheLocation::ANY;
        }

        if (array_search($location, array(OutputCacheLocation::ANY, OutputCacheLocation::SERVER, OutputCacheLocation::SERVER_AND_CLIENT)) === false) {
            return false;
        }

        // vary by parameters
        $params = self::getCacheParams($actionContext, $cacheSettings, 'varyByParam', array_merge($request->get(), $actionContext->getArguments()));

        // vary by headers
        $headers = self::getCacheParams($actionContext, $cacheSettings, 'varyByHeader', $request->headers());

        // vary by custom handlers
        $custom = self::getCacheParams($actionContext, $cacheSettings, 'varyByCustom');

        // check cache
        $response = $httpContext->getResponse();
        $cache = $httpContext->getCache();

        $cacheKey = 'output_' . $request->path() . '_params=' . implode('&', $params) . '_http=' . implode('&', $headers) . '_custom=' . implode('&', $custom);

        if (($cachedResponse = $cache->get($cacheKey)) !== null) {
            $response->setHeaders($cachedResponse['headers']);
            $response->setCookies($cachedResponse['cookies']);
            $response->setStatusCode($cachedResponse['statusCode']);
            $response->setStatusDescription($cachedResponse['statusDescription']);
            $response->writeFile($cachedResponse['files']);
            $response->write($cachedResponse['output']);
            $response->end();
            return true;
        }

        InternalHelper::setPropertyValue($actionContext, 'cacheKey', $cacheKey);

        return false;
    }

    /**
     * Caches the response, if possible.
     * 
     * @return void
     */
    private static function caching($actionContext) {
        $cacheKey = InternalHelper::getPropertyValue($actionContext, 'cacheKey');

        // no key, no cache
        if (empty($cacheKey)) {
            return;
        }

        $cacheSettings = InternalHelper::getStaticPropertyValue('\\PhpMvc\\OutputCache', 'settings');
        $actionName = $actionContext->getActionName();
        $httpContext = $actionContext->getHttpContext();
        $response = $httpContext->getResponse();
        $cache = $httpContext->getCache();

        // cache duration
        if (!empty($cacheSettings[$actionName]['duration'])) {
            $duration = intval($cacheSettings[$actionName]['duration']);
        }
        elseif (!empty($cacheSettings['.']['duration'])) {
            $duration = intval($cacheSettings['.']['duration']);
        }
        else {
            $duration = 0;
        }

        // TODO: regionName
        $regionName = null;

        $dataToCache = array();

        // add response to cache
        if (($cachePartSeq = $cache->get($cacheKey . '_part_seq')) === null) {
            $dataToCache['statusCode'] = $response->getStatusCode();
            $dataToCache['statusDescription'] = $response->getStatusDescription();
            $dataToCache['headers'] = $response->getHeaders();
            $dataToCache['cookies'] = $response->getCookies();
            $dataToCache['files'] = InternalHelper::getPropertyValue($response, 'files');
            $dataToCache['output'] = InternalHelper::getPropertyValue($response, 'output');
        }
        else {
            $dataToCache['statusCode'] = $response->getStatusCode();
            $dataToCache['statusDescription'] = $response->getStatusDescription();
            $dataToCache['headers'] = $response->getHeaders();
            $dataToCache['cookies'] = $response->getCookies();
            $dataToCache['files'] = array();
            $dataToCache['output'] = '';

            for ($i = 0; $i < $cachePartSeq; ++$i) {
                $part = $cache->get($cacheKey . '_part_' . $i);

                if ($part !== null) {
                    $dataToCache['output'] .= $part['output'];
                    $dataToCache['files'] = array_merge($dataToCache['files'], $part['files']);
                    $cache->remove($cacheKey . '_part_' . $i);
                }
            }

            $cache->remove($cacheKey . '_part_seq');
        }

        $cache->add($cacheKey, $dataToCache, $duration, $regionName);
    }

    /**
     * Caches the output in parts.
     * 
     * @return void
     */
    private static function cachingPartial($actionContext, $eventArgs) {
        $cacheKey = InternalHelper::getPropertyValue($actionContext, 'cacheKey');

        // no key, no cache
        if (empty($cacheKey)) {
            return;
        }

        $cache = $httpContext->getCache();
        $cachePartSeq = $cache->get($cacheKey . '_part_seq');

        if (!isset($cachePartSeq)) {
            $cachePartSeq = -1;
        }

        $dataToCache = array();
        $dataToCache['output'] .= $eventArgs['output'];
        $dataToCache['files'] = array_merge($dataToCache['files'], $eventArgs['files']);

        $cache->add($cacheKey . '_part_' . $cachePartSeq, $dataToCache, 30);
        $cache->add($cacheKey . '_part_seq', (int)$cachePartSeq + 1, 30);
    }

    /**
     * Sends the cache headers.
     * 
     * @return void
     */
    private static function cachingClient($actionContext) {
        $cacheSettings = InternalHelper::getStaticPropertyValue('\\PhpMvc\\OutputCache', 'settings');
        $actionName = $actionContext->getActionName();

        // location
        if (!empty($cacheSettings[$actionName]['location'])) {
            $location = intval($cacheSettings[$actionName]['location']);
        }
        elseif (!empty($cacheSettings['.']['location'])) {
            $location = intval($cacheSettings['.']['location']);
        }
        else {
            $location = OutputCacheLocation::ANY;
        }

        // duration
        if (!empty($cacheSettings[$actionName]['duration'])) {
            $duration = intval($cacheSettings[$actionName]['duration']);
        }
        elseif (!empty($cacheSettings['.']['duration'])) {
            $duration = intval($cacheSettings['.']['duration']);
        }
        else {
            $duration = 0;
        }

        // send headers
        $httpContext = $actionContext->getHttpContext();
        $response = $httpContext->getResponse();
        $cacheControl = array();

        switch ($location) {
            case OutputCacheLocation::ANY:
            case OutputCacheLocation::DOWNSTREAM:
                $cacheControl[] = 'public';
                break;

            case OutputCacheLocation::NONE:
                $cacheControl[] = 'no-cache';
                $cacheControl[] = 'must-revalidate';
                $pragma = 'no-cache';
                break;

            case OutputCacheLocation::SERVER:
                $cacheControl[] = 'no-cache';
                $pragma = 'no-cache';
                break;

            case OutputCacheLocation::CLIENT:
            case OutputCacheLocation::SERVER_AND_CLIENT:
                $cacheControl[] = 'private';
                break;
        }

        if ($duration > 0) {
            $cacheControl[] = 'max-age=' . $duration;
        }

        $response->addHeader('Cache-Control', implode(', ', $cacheControl));

        if (isset($pragma)) {
            $response->addHeader('Pragma', $pragma);
        }
    }

    /**
     * Builds an array of parameters for the cache entry key.
     * 
     * @return array
     */
    private static function getCacheParams($actionContext, $cacheSettings, $parameterName, $value = null) {
        $actionName = $actionContext->getActionName();
        $params = array();

        if ($parameterName != 'varyByCustom') {
            ksort($value);

            // action level
            if (!empty($cacheSettings[$actionName][$parameterName])) {
                if ($cacheSettings[$actionName][$parameterName] == '*') {
                    // all parameters
                    $params = $value;
                    $allParameters = true;
                }
                else {
                    // specified parameters
                    $varyByParam = array_map('trim', explode(';', $cacheSettings[$actionName][$parameterName]));
                    sort($varyByParam);
    
                    foreach ($varyByParam as $param) {
                        $params[$param] = $param . '=' . (isset($value[$param]) ? $value[$param] : '');
                    }
                }
            }
    
            // controller level
            if (!isset($allParameters) && !empty($cacheSettings['.'][$parameterName])) {
                if ($cacheSettings['.'][$parameterName] == '*') {
                    // all parameters
                    $params = $value;
                }
                else {
                    // specified parameters
                    $varyByParam = array_map('trim', explode(';', $cacheSettings['.'][$parameterName]));
                    sort($varyByParam);
    
                    foreach ($varyByParam as $param) {
                        if (empty($params[$param])) {
                            $params[$param] = $param . '=' . (isset($value[$param]) ? $value[$param] : '');
                        }
                    }
                }
            }
        }
        else {
            if (!empty($cacheSettings[$actionName][$parameterName]) && is_callable($cacheSettings[$actionName][$parameterName])) {
                $params[] = '0=' . $cacheSettings[$actionName][$parameterName]($actionContext);
            }

            if (empty($params[0]) && !empty($cacheSettings['.'][$parameterName]) && is_callable($cacheSettings['.'][$parameterName])) {
                $params[] = '0=' . $cacheSettings['.'][$parameterName]($actionContext);
            }
        }

        return $params;
    }

    /**
     * Initializes the filters.
     * 
     * @return void
     */
    private static function filters($actionContext) {
        $allFilters = InternalHelper::getStaticPropertyValue('\\PhpMvc\\Filter', 'filters');
        $allFiltersInstance = array();

        foreach ($allFilters as $actionName => $filters) {
            foreach ($filters as $filterName) {
                $className = $filterName;

                if (!class_exists($className)) {
                    $className = '\\' . PHPMVC_APP_NAMESPACE . '\\Filters\\' . $className;

                    if (!class_exists($className)) {
                        throw new \Exception('Filter "' . $filterName . '" not found.');
                    }
                }

                $filterClass = new \ReflectionClass($className);

                if (!$filterClass->isSubclassOf('\\PhpMvc\\ActionFilter')) {
                    throw new \Exception('The filter type must be the base of "\PhpMvc\ActionFilter".');
                }

                if ($filterClass->getConstructor() != null) {
                    $filterInstance = $filterClass->newInstance();
                }
                else {
                    $filterInstance = $filterClass->newInstanceWithoutConstructor();
                }

                $allFiltersInstance[] = $filterInstance;
            }
        }

        InternalHelper::setPropertyValue($actionContext, 'filters', $allFiltersInstance);
    }

    /**
     * Checks the request and throws an exception if the request contains dangerous data.
     * 
     * @return void
     */
    private static function validation() {
        if (isset(self::$config['validators'])) {
            $validators = self::$config['validators'];
        }
        else {
            $validators = true;
        }

        // action name validation
        if ($validators === true || (!isset($validators['actionName']) || $validators['actionName'] === true)) {
            if (substr(PHPMVC_ACTION, 0, 2) == '__') {
                throw new ActionNameValidationException();
            }
        }

        // request verification token
        $antiForgeryTokenForAction = InternalHelper::getStaticPropertyValue('\\PhpMvc\\ValidateAntiForgeryToken', 'enable');
        if ($antiForgeryTokenForAction === true || (($validators === true || (!isset($validators['antiForgeryToken']) || $validators['antiForgeryToken'] === true)) && $antiForgeryTokenForAction !== false)) {
            $request = self::$config['httpContext']->getRequest();

            if ($request->isPost()) {
                $post = $request->post();
                $expected = $request->cookies('__requestVerificationToken');

                if (!isset($post)) { $post = array(); }

                if (
                    (isset($expected) && $expected !== 'false' && (!isset($post['__requestVerificationToken']) || $post['__requestVerificationToken'] != $expected)) ||
                    (isset($expected) && $expected === 'false' && !empty($post['__requestVerificationToken'])) ||
                    (isset($post['__requestVerificationToken']) && empty($expected))
                   ) {
                    throw new HttpAntiForgeryException();
                }
            }
        }

        // cross site scripting validation
        if ($validators === true || (!isset($validators['crossSiteScripting']) || $validators['crossSiteScripting'] === true)) {
            $request = self::$config['httpContext']->getRequest();

            $get = $request->get();
            $post = $request->post();

            if (!isset($get)) { $get = array(); }
            if (!isset($post)) { $post = array(); }

            $items = array_merge($get, $post);

            foreach ($items as $key => $value) {
                $isDangerousString = CrossSiteScriptingValidation::IsDangerousString($key) || CrossSiteScriptingValidation::IsDangerousString($value);
    
                if ($isDangerousString) {
                    $source = null;

                    if (!empty($get[$key])) {
                        $source = '$_GET';
                    }
                    elseif (!empty($post[$key])) {
                        $source = '$_POST';
                    }
    
                    throw new HttpRequestValidationException($source, $key, $value);
                }
            }
        }
    }

    /**
     * Includes the required files. 
     * 
     * @return void
     */
    private static function dependencies() {
        require_once PHPMVC_CORE_PATH . 'Loader.php';
        require_once PHPMVC_CORE_PATH . 'Controller.php';
    }

    /**
     * Generates and renders the final result.
     * 
     * @param ActionContext $actionContext The action context.
     * 
     * @return void
     */
    private static function render($actionContext) {
        $controller = $actionContext->getController();

        // annotation and validation
        $modelState = $actionContext->getModelState();
        self::annotateAndValidateModel($modelState);

        // filters
        $actionExecutingContext = self::actionExecutingFilters($actionContext);

        if (($actionResult = $actionExecutingContext->getResult()) === null) {
            try {
                // execute action
                $actionResult = self::executeAction($actionContext);
                // filters
                $actionExecutedContext = self::actionExecutedFilters($actionContext, $actionResult);
                // set result
                $actionResult = $actionExecutedContext->getResult();
            }
            catch (\Exception $ex) {
                // set status code
                $actionContext->getHttpContext()->getResponse()->setStatusCode(500);
                // add the error to the modelState
                $modelState->addError('.', $ex);
                // filters
                $exceptionContext = self::exceptionFilters($actionContext, $ex);
                if (($actionResult = $exceptionContext->getResult()) === null) {
                    if ($exceptionContext->getExceptionHandled()) {
                        // executed filters
                        $actionExecutedContext = self::actionExecutedFilters($actionContext, $actionResult, $exceptionContext);
                        // set result
                        $actionResult = $actionExecutedContext->getResult();
                    }
                    else {
                        $actionResult = new ExceptionResult($ex);
                    }
                }
            }
        }

        if (isset($actionResult)) {
            // transformation of known types
            if (is_callable($actionResult)) {
                $actionResult = $actionResult();
            }

            if (is_scalar($actionResult)) {
                $actionResult = new ContentResult($actionResult, 'text/plain');
            }
            if ((($resultType = gettype($actionResult)) === 'object' || $resultType === 'array') && !$actionResult instanceof ActionResult) {
                $actionResult = new JsonResult($actionResult);
            }

            // make result
            if ($actionResult instanceof ViewResult || $actionResult instanceof ExceptionResult) {
                // create view data
                $viewData = $modelState->getKeyValuePair();
                $controllerViewData = InternalHelper::getPropertyValueOfParentClass($controller, 'viewData');

                if (!empty($controllerViewData)) {
                    $viewData = array_unique(array_merge($viewData, $controllerViewData), \SORT_STRING);
                }

                // create view
                $viewContext = InternalHelper::makeViewContext(
                    (!empty($actionResult->viewFile) ? $actionResult->viewFile : PHPMVC_CURRENT_VIEW_PATH),
                    $actionContext,
                    $actionResult,
                    null,
                    $viewData,
                    null,
                    null,
                    null
                );

                // get content
                $result = InternalHelper::getTopLevelViewContext($viewContext)->content;

                // render
                $response = $actionContext->getHttpContext()->getResponse();
                $response->write($result);
                $response->end();
            }
            elseif ($actionResult instanceof ActionResult) {
                // execute result with action context
                $actionResult->execute($actionContext);
            }
        }
    }

    /**
     * Makes actions state.
     * 
     * @param ActionContext $actionContext Action context and descriptor.
     * 
     * @return void
     */
    private static function makeActionState($actionContext) {
        $arguments = array();
        $modelState = new ModelState();
        $modelState->annotations = $actionContext->getModelState()->annotations;
        $hasModel = false;
        $request = $actionContext->getHttpContext()->getRequest();

        // route
        $route = $actionContext->getRoute();
        $segments = $route->getSegments(true);

        // params of get http
        // TODO: подумать о возможности управлять этим
        $get = $request->get();

        // change case of keys
        $get = array_change_key_case($get, CASE_LOWER);

        // post
        $isPost = $request->isPost();

        // get params of action method
        $r = new \ReflectionMethod(get_class($actionContext->getController()), $actionContext->getActionName());

        $methodParams = $r->getParameters();

        // search for the necessary parameters
        foreach ($methodParams as $param) {
            $name = strtolower($param->getName());

            if (isset($route->values[$name])) {
                $modelState[$name] = new ModelStateEntry($name, $arguments[$name] = $route->values[$name]);
            }
            elseif (isset($get[$name])) {
                $modelState[$name] = new ModelStateEntry($name, $arguments[$name] = $get[$name]);
            }
            else {
                // parameter not found
                if ($isPost && !$hasModel && !isset($get[$name]) && !isset($segments[$name])) {
                    $postData = $request->post();
                    $paramTypeName = '\stdClass';
                    $post = null;

                    if (isset($postData) && isset($postData['__requestVerificationToken'])) {
                        unset($postData['__requestVerificationToken']);
                    }

                    if (method_exists($param, 'hasType') && $param->hasType()) {
                        if ($param->getType()->isBuiltin()) {
                            $arguments[$name] = ($param->isOptional() ? $param->getDefaultValue() : null);
                            continue;
                        }

                        if (($paramTypeName = $param->getClass()) !== null) {
                            $paramTypeName = $paramTypeName->getName();
                        }
                    }
                    else {
                        if (($paramTypeName = $param->getClass()) !== null) {
                            $paramTypeName = $paramTypeName->getName();
                        }
                    }

                    if (!empty($postData)) {
                        InternalHelper::arrayToObject($postData, $post, $paramTypeName);
                    }
                    
                    if (empty($post)) {
                        $contentType = $request->contentType();
            
                        if (strrpos($contentType, '/json') !== false) {
                            $requestBody = file_get_contents('php://input');
                            InternalHelper::arrayToObject(json_decode($requestBody, true), $post, $paramTypeName);
                        }
                        else {
                            // TODO...
                        }
                    }

                    // post method and model not yet received
                    if ($post == null) {
                        throw new \Exception('"' . $name . ' is required.');
                    }

                    $arguments[$name] = $post;

                    // parse post data
                    self::parseObjectToModelState($modelState, $post);

                    $hasModel = true;
                    continue;
                }

                if ($param->isOptional()) {
                    // is optional parameter, skip it
                    $arguments[$name] = $param->getDefaultValue();
                    continue;  
                }

                // required parameter
                // TODO: config
                // throw new \ErrorException('"'.$name.'" is required.');
                // at the moment, just add null to args
                // and skip for model state
                $arguments[$name] = null;
            }
        }

        InternalHelper::setPropertyValue($actionContext, 'arguments', $arguments);
        InternalHelper::setPropertyValue($actionContext, 'modelState', $modelState);
    }

    private static function parseObjectToModelState(&$modelState, $object, $keys = array()) {
        foreach (get_object_vars($object) as $key => $value) {
            if (is_object($value)) {
                self::parseObjectToModelState($modelState, $value, array_merge($keys, array($key)));
            }
            else {
                $key = implode('_', array_merge($keys, array($key)));
                $modelState[$key] = new ModelStateEntry($key, $value);
            }
        }
    }

    /**
     * @param ActionContext $actionContext Action context and descriptor.
     * 
     * @return void
     */
    private static function annotateAndValidateModel($modelState) {
        foreach ($modelState->items as $item) {
            self::annotateAndValidateModelEntry($modelState, $item->key);
        }
    }

    /**
     * Checks the state of the model element and sets the result of the validation to the model.
     * 
     * @param ModelState $modelState
     * @param string $key
     * 
     * @return void
     */
    private static function annotateAndValidateModelEntry($modelState, $key) {
        $entry = $modelState[$key];

        if (($dataAnnotation = $modelState->getAnnotation($key)) !== null) {
            $displayName = $key;

            if (!empty($dataAnnotation->displayName)) {
                $displayName = $dataAnnotation->displayName;
            }

            // required field
            if (isset($dataAnnotation->required) && !($entry->validationState = !empty($entry->value))) {
                if (!empty($dataAnnotation->required[0])) {
                    $modelState->addError($key, $dataAnnotation->required[0]);
                }
                else {
                    $modelState->addError($key, $displayName . ' is required. Value must not be empty.');
                }
            }

            // compareWith
            if (!empty($dataAnnotation->compareWith)) {
                if (!empty($entry2 = $modelState[$dataAnnotation->compareWith[0]])) {
                    if (!($entry->validationState = ($entry->value == $entry2->value))) {
                        $displayName2 = $entry2->key;

                        if (($dataAnnotation2 = $modelState->getAnnotation($entry2->key)) !== null) {
                            if (!empty($dataAnnotation2->displayName)) {
                                $displayName2 = $dataAnnotation2->displayName;
                            }
                        }

                        if (!empty($dataAnnotation->compareWith[1])) {
                            $modelState->addError($key, $dataAnnotation->compareWith[1]);
                        } else {
                            $modelState->addError($key, 'The value of '. $displayName2 . ' must match the value of ' . $displayName . '.');
                        }
                    }
                }
                else {
                    // TODO: exception or not exception?
                }
            }

            // stringLength
            if (!empty($dataAnnotation->stringLength)) {
                $max = (int)$dataAnnotation->stringLength[0];
                $min = (int)$dataAnnotation->stringLength[1];
                $len = strlen($entry->value);

                if (!($entry->validationState = ($len >= $min && $len <= $max))) {
                    if (!empty($dataAnnotation->stringLength[2])) {
                        $modelState->addError($key, $dataAnnotation->stringLength[2]);
                    }
                    else {
                        // TODO: split messages (max only if min is zero)
                        $modelState->addError($key, 'The value must be at least ' . $min . ' character and not more than ' . $max . ' characters.');
                    }
                }
            }

            // range
            if (!empty($dataAnnotation->range)) {
                $min = (int)$dataAnnotation->range[0];
                $max = (int)$dataAnnotation->range[1];
                $value = (int)$entry->value;
                
                if (!($entry->validationState = ($value >= $min && $value <= $max))) {
                    if (!empty($dataAnnotation->range[2])) {
                        $modelState->addError($key, $dataAnnotation->range[2]);
                    }
                    else {
                        $modelState->addError($key, 'The value must be between ' . $min . ' and ' . $max . '.');
                    }
                }
            }

            // custom validation
            if (!empty($dataAnnotation->customValidation)) {
                $errorMessage = $displayName . ' is not valid.';

                if (!empty($dataAnnotation->customValidation[1])) {
                    $errorMessage = $dataAnnotation->customValidation[1];
                }

                try {
                    $entry->validationState = $dataAnnotation->customValidation[0]($entry->value, $errorMessage);
                }
                catch (\Exception $ex) {
                    $modelState->addError($key, $ex);
                    $entry->validationState = false;
                }

                if (!$entry->validationState) {
                    $modelState->addError($key, $errorMessage);
                }
            }
        }
    }

    /**
     * Calls the action method and returns the result.
     * 
     * @return mixed
     */
    private static function executeAction($actionContext) {
        $controller = $actionContext->getController();
        $actionName = $actionContext->getActionName();
        $action = new \ReflectionMethod($controller, $actionName);

        if (!$action->isPublic()) {
            throw new \Exception('Action methods must have a public modifier. The action call "' . $actionName . '" is denied.');
        }

        return $action->invokeArgs($controller, $actionContext->getArguments());
    }

    /**
     * Calls the actionExecuting methods.
     * 
     * @param ActionContext $actionContext The context of the action.
     * 
     * @return ActionExecutingContext
     */
    private static function actionExecutingFilters($actionContext) {
        $context = new ActionExecutingContext($actionContext);

        $filters = $actionContext->getFilters();

        foreach ($filters as $filter) {
            $filter->actionExecuting($context);
        }

        return $context;
    }

    /**
     * Calls the actionExecuted methods.
     * 
     * @param ActionContext $actionContext The context of the action.
     * @param ActionResult $actionResult The result of action.
     * @param ExceptionContext $exceptionContext The context of exception.
     * 
     * @return ActionExecutedContext
     */
    private static function actionExecutedFilters($actionContext, $actionResult, $exceptionContext = null) {
        $context = new ActionExecutedContext($actionContext, $actionResult, $exceptionContext);

        $filters = $actionContext->getFilters();

        foreach ($filters as $filter) {
            $filter->actionExecuted($context);
        }

        return $context;
    }

    /**
     * Calls the exception methods.
     * 
     * @param ActionContext $actionContext The context of the action.
     * @param \Exception $exception The exception.
     * 
     * @return ExceptionContext
     */
    private static function exceptionFilters($actionContext, $exception) {
        $context = new ExceptionContext($actionContext, $exception);
        
        $filters = $actionContext->getFilters();

        foreach ($filters as $filter) {
            $filter->exception($context);
        }

        return $context;
    }

    /**
     * @param callback[] $callbacks
     * 
     * @return void
     */
    private static function invokeAll($callbacks, $parameters = array()) {
        foreach ($callbacks as $callback) {
            call_user_func_array($callback, $parameters);
        }
    }

}