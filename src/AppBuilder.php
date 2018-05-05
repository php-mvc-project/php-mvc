<?php
namespace PhpMvc;

/**
 * The main class that works wonders.
 */
final class AppBuilder {

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
     * Registers routes.
     * 
     * @param callback $routes A function in which an instance of the route provider will be passed, through which routing rules are created.
     * For example:
     * AppBuilder::routes(function($routes) {
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
        self::init();
        self::include();

        $route = self::canRoute();

        if ($route !== false) {
            $actionContext = self::actionContext($route);

            if (!self::cached($actionContext)) {
                self::filters($actionContext);
                self::headers();
                self::validation();
                self::render($actionContext);
            }
        }
    }

    /**
     * Adds headers.
     * 
     * @return void
     */
    private static function headers() {
        $response = self::$config['httpContext']->getResponse();
        $response->addHeader('X-Powered-By', Info::XPOWEREDBY);
    }

    /**
     * Performs the initialization of the builder.
     * 
     * @return void
     */
    private static function init() {
        if (empty(self::$config['appNamespace'])) {
            throw new \Exception('The root namespace for the application is required. To specify the namespace of your application, use the useNamespace method. The value must not be null or empty.');
        }

        if (empty(self::$config['basePath'])) {
            self::$config['basePath'] = getcwd();
        }

        if (empty(self::$config['routeProvider'])) {
            self::$config['routeProvider'] = new DefaultRouteProvider();
        }
        elseif (!self::$config['routeProvider'] instanceof RouteProvider) {
            throw new \Exception('The routeProvider type must be the base of "\PhpMvc\RouteProvider".');
        }

        if (isset(self::$config['routes'])) {
            if (is_callable(self::$config['routes'])) {
                self::$config['routes'](self::$config['routeProvider']);
            }
            elseif (is_array(self::$config['routes'])) {
                $provider = self::$config['routeProvider'];

                foreach(self::$config['routes'][0] as $route) {
                    $provider->add($route->name, $route->template, $route->defaults, $route->constraints);
                }

                if (count(self::$config['routes']) > 1) {
                    foreach(self::$config['routes'][1] as $route) {
                        $provider->ingnore($route->template, $route->constraints);
                    }
                }
            }
            elseif (self::$config['routes'] instanceof RouteCollection) {
                $provider = self::$config['routeProvider'];

                foreach(self::$config['routes'] as $route) {
                    $provider->add($route->name, $route->template, $route->defaults, $route->constraints);
                }
            }
        }

        if (empty(self::$config['cacheProvider'])) {
            self::$config['cacheProvider'] = new IdleCacheProvider();
        }
        elseif (!self::$config['cacheProvider'] instanceof CacheProvider) {
            throw new \Exception('The $cacheProvider type must be the base of "\PhpMvc\CacheProvider".');
        }

        if (empty(self::$config['httpContext'])) {
            $info = new HttpContextInfo();
            $info->routeProvider = self::$config['routeProvider'];
            $info->cacheProvider = self::$config['cacheProvider'];
            $info->request = new HttpRequest();
            $info->response = new HttpResponse();

            self::$config['httpContext'] = new HttpContext($info);
        }
        elseif (!self::$config['httpContext'] instanceof HttpContextBase) {
            throw new \Exception('The httpContext type must be the base of "\PhpMvc\HttpContextBase".');
        }

        if (!defined('PHPMVC_DS')) { define('PHPMVC_DS', DIRECTORY_SEPARATOR); }
        if (!defined('PHPMVC_ROOT_PATH')) { define('PHPMVC_ROOT_PATH', self::$config['basePath'] . PHPMVC_DS); }
        if (!defined('PHPMVC_CORE_PATH')) { define('PHPMVC_CORE_PATH', __DIR__ .PHPMVC_DS); }
        if (!defined('PHPMVC_CONFIG_PATH')) { define('PHPMVC_CONFIG_PATH', PHPMVC_ROOT_PATH . 'config' . PHPMVC_DS); }
        if (!defined('PHPMVC_FILTER_PATH')) { define('PHPMVC_FILTER_PATH', PHPMVC_ROOT_PATH . 'filters' . PHPMVC_DS); }
        if (!defined('PHPMVC_CONTROLLER_PATH')) { define('PHPMVC_CONTROLLER_PATH', PHPMVC_ROOT_PATH . 'controllers' . PHPMVC_DS); }
        if (!defined('PHPMVC_MODEL_PATH')) { define('PHPMVC_MODEL_PATH', PHPMVC_ROOT_PATH . 'models' . PHPMVC_DS); }
        if (!defined('PHPMVC_VIEW_PATH')) { define('PHPMVC_VIEW_PATH', PHPMVC_ROOT_PATH . 'views' . PHPMVC_DS); }
        if (!defined('PHPMVC_SHARED_PATH')) { define('PHPMVC_SHARED_PATH', PHPMVC_VIEW_PATH . 'shared' . PHPMVC_DS); }
        if (!defined('PHPMVC_UPLOAD_PATH')) { define('PHPMVC_UPLOAD_PATH', PHPMVC_ROOT_PATH . 'upload' . PHPMVC_DS); }

        if (!defined('PHPMVC_APP_NAMESPACE')) {
            define('PHPMVC_APP_NAMESPACE', self::$config['appNamespace']);
        }
        elseif (PHPMVC_APP_NAMESPACE !== self::$config['appNamespace']) {
            throw new \Exception('Constant PHPMVC_CONTROLLER already defined. Re-define with other value is not possible.');
        }

        InternalHelper::setStaticPropertyValue('\\PhpMvc\\HttpContext', 'current', self::$config['httpContext']);
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

        // TODO: kill constants
        if (!defined('PHPMVC_CONTROLLER')) { define('PHPMVC_CONTROLLER', ucfirst($route->getValueOrDefault('controller', 'Home'))); }
        if (!defined('PHPMVC_ACTION')) { define('PHPMVC_ACTION', $route->getValueOrDefault('action', 'index')); }
        if (!defined('PHPMVC_VIEW')) { define('PHPMVC_VIEW', strtolower(PHPMVC_CONTROLLER)); }
        if (!defined('PHPMVC_CURRENT_CONTROLLER_PATH')) { define('PHPMVC_CURRENT_CONTROLLER_PATH', PHPMVC_CONTROLLER_PATH . PHPMVC_VIEW . PHPMVC_DS); }
        if (!defined('PHPMVC_CURRENT_VIEW_PATH')) { define('PHPMVC_CURRENT_VIEW_PATH', PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . PHPMVC_ACTION . '.php'); }

        // create action context
        $actionContext = new ActionContext($httpContext);
        InternalHelper::setPropertyValue($actionContext, 'actionName', PHPMVC_ACTION);

        // preparing to create an instance of the controller class 
        $controllerClass = new \ReflectionClass('\\' . PHPMVC_APP_NAMESPACE . '\\Controllers\\' . PHPMVC_CONTROLLER . 'Controller');

        if (!$controllerClass->isSubclassOf('\\PhpMvc\\Controller')) {
            throw new \Exception('The controller type must be the base of "\\PhpMvc\\Controller".');
        }

        $actionContextProperty = $controllerClass->getParentClass()->getProperty('actionContext');
        $actionContextProperty->setAccessible(true);

        // set action context
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Model', 'actionContext', $actionContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Filter', 'actionContext', $actionContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\OutputCache', 'actionContext', $actionContext);

        // create instance of controller
        if ($controllerClass->getConstructor() != null) {
            $controllerInstance = $controllerClass->newInstance();
        }
        else {
            $controllerInstance = $controllerClass->newInstanceWithoutConstructor();
        }

        $actionContextProperty->setValue($controllerInstance, $actionContext);

        InternalHelper::setPropertyValue($actionContext, 'controller', $controllerInstance);

        // get and set model annotations
        $annotations = InternalHelper::getStaticPropertyValue('\\PhpMvc\\Model', 'annotations');
        $modelState = $actionContext->getModelState();
        $modelState->annotations = $annotations;
        // InternalHelper::setPropertyValue($modelState, 'annotations', $annotations);

        // response handling
        $httpContext->getResponse()->setFlushHandler(function($eventArgs) use ($actionContext) {
            self::cachingPartial($actionContext, $eventArgs);
        });

        $httpContext->getResponse()->setEndHandler(function() use ($actionContext) {
            self::caching($actionContext);
        });

        $httpContext->getResponse()->setPreSendHandler(function() use ($actionContext) {
            self::cachingClient($actionContext);
        });

        // arguments and model state
        self::makeActionState($actionContext);

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
        if (substr(PHPMVC_ACTION, 0, 2) == '__') {
            throw new \Exception('Action names can not begin with a "_".');
        }
    }

    /**
     * Includes the required files. 
     * 
     * @return void
     */
    private static function include() {
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
                    PHPMVC_CURRENT_VIEW_PATH,
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

        // route values
        $route = $actionContext->getRoute();

        // params of get http
        // TODO: подумать о возможности управлять этим
        $get = $request->get();

        // change case of keys
        $get = array_change_key_case($get, CASE_LOWER);

        // params of post
        $isPost = $request->isPost();
        $postData = $isPost ? $request->post() : null;
        $post =  null;

        if (!empty($postData)) {
            $post = InternalHelper::arrayToObject($postData);
        }
        
        if (empty($post) && $isPost) {
            $contentType = $request->contentType();

            if (strrpos($contentType, '/json') !== false) {
                $requestBody = file_get_contents('php://input');

                $post = json_decode($requestBody);
            }
            else {
                // TODO...
            }
        }

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
                if ($isPost && !$hasModel && !isset($get[$name])) {
                    // post method and model not yet received
                    if ($post == null) {
                        throw new \Exception('"' . $name . ' is required.');
                    }

                    $arguments[$name] = $post;
                    
                    // parse post data
                    foreach (get_object_vars($post) as $key => $value) {
                        $modelState[$key] = new ModelStateEntry($key, $value);
                    }
                    
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

}