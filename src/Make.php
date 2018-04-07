<?php
namespace PhpMvc;

/**
 * The main class that works wonders.
 */
final class Make {

    /**
     * Defines the ActionContext.
     * 
     * @var ActionContext
     */
    private static $actionContext;

    /**
     * Gets or sets request of the HTTP context.
     * 
     * @var HttpRequestBase
     */
    private static $request;

    /**
     * Gets or sets response of the HTTP context.
     * 
     * @var HttpResponseBase
     */
    private static $response;

    /**
     * Magic!
     * 
     * @param string $appNamespace Root namespace of the application.
     * 
     * @return void
     */
    public static function magic($appNamespace, $httpContext = null, $basePath = null, $routes = null) {
        self::init($appNamespace, $basePath);
        self::context($httpContext, $routes);
        self::headers();
        self::validation();
        self::include();
        self::render();
    }

    /**
     * Adds headers.
     * 
     * @return void
     */
    private static function headers() {
        self::$response->addHeader('X-Powered-By', Info::XPOWEREDBY);
    }

    /**
     * Performs the initialization of the engine.
     * 
     * @param string $appNamespace Root namespace of the application.
     * @param string $basePath Base path.
     * 
     * @return void
     */
    private static function init($appNamespace, $basePath = null) {
        if (empty($basePath)) {
            $basePath = getcwd();
        }

        define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        define('PHPMVC_ROOT_PATH', $basePath . PHPMVC_DS);
        define('PHPMVC_CORE_PATH', __DIR__ .PHPMVC_DS);
        define('PHPMVC_CONFIG_PATH', PHPMVC_ROOT_PATH . 'config' . PHPMVC_DS);
        define('PHPMVC_CONTROLLER_PATH', PHPMVC_ROOT_PATH . 'controllers' . PHPMVC_DS);
        define('PHPMVC_MODEL_PATH', PHPMVC_ROOT_PATH . 'models' . PHPMVC_DS);
        define('PHPMVC_VIEW_PATH', PHPMVC_ROOT_PATH . 'views' . PHPMVC_DS);
        define('PHPMVC_SHARED_PATH', PHPMVC_VIEW_PATH . 'shared' . PHPMVC_DS);
        define('PHPMVC_UPLOAD_PATH', PHPMVC_ROOT_PATH . 'upload' . PHPMVC_DS);

        define('PHPMVC_APP_NAMESPACE', $appNamespace);
    }

    /**
     * Initializes the contexts associated with the current request.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * @param RouteCollection $routes
     * 
     * @return void
     */
    private static function context($httpContext = null, $routes = null) {
        // check request context
        if (isset($httpContext)) {
            if (!$httpContext instanceof HttpContextBase) {
                throw new \Exception('The $httpContext type must be derived from "\PhpMvc\HttpContextBase".');
            }
        }
        else {
            $httpContext = new HttpContext();
        }

        self::$request = $httpContext->getRequest();
        self::$response = $httpContext->getResponse();

        if (isset($routes)) {
            if (!$routes instanceof RouteCollection) {
                throw new \Exception('The $routes type must be derived from "\PhpMvc\RouteCollection".');
            }
        }
        else {
            $routesProperty = new \ReflectionProperty('\PhpMvc\RouteTable', 'routes');
            $routesProperty->setAccessible(true);
            $routes = $routesProperty->getValue(null);
        }

        // search route
        $route = $routes->getRoute($httpContext);

        if ($route == null) {
            self::$response->setStatusCode(404);
            self::$response->end();
        }

        define('PHPMVC_CONTROLLER', ucfirst($route->getValueOrDefault('controller', 'Home')));
        define('PHPMVC_ACTION', $route->getValueOrDefault('action', 'index'));
        define('PHPMVC_VIEW', strtolower(PHPMVC_CONTROLLER));
        define('PHPMVC_CURRENT_CONTROLLER_PATH', PHPMVC_CONTROLLER_PATH . PHPMVC_VIEW . PHPMVC_DS);
        define('PHPMVC_CURRENT_VIEW_PATH', PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . PHPMVC_ACTION . '.php');

        // create action context
        self::$actionContext = $actionContext = new ActionContext($httpContext, $route);

        // preparing to create an instance of the controller class 
        $controllerClass = new \ReflectionClass('\\' . PHPMVC_APP_NAMESPACE . '\\Controllers\\' . PHPMVC_CONTROLLER . 'Controller');
        
        if (!$controllerClass->isSubclassOf('\\PhpMvc\\Controller')) {
            throw new \Exception('The controller type must be derived from "\PhpMvc\Controller".');
        }

        $actionContextProperty = $controllerClass->getProperty('actionContext');
        $actionContextProperty->setAccessible(true);

        // set action context to model
        $modelActionContextProperty = new \ReflectionProperty('\PhpMvc\Model', 'actionContext');
        $modelActionContextProperty->setAccessible(true);
        $modelActionContextProperty->setValue(null, $actionContext);

        // create instance of controller
        if ($controllerClass->getConstructor() != null) {
            $controllerInstance = $controllerClass->newInstance();
        }
        else {
            $controllerInstance = $controllerClass->newInstanceWithoutConstructor();
        }

        $actionContextProperty->setValue($controllerInstance, $actionContext);

        $actionContext->controller = $controllerInstance;
        $actionContext->actionName = PHPMVC_ACTION;
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
     * @return void
     */
    private static function render() {
        $result = null;

        $actionContext = self::$actionContext;

        // arguments and model state
        self::makeActionState($actionContext);

        // annotation and validation
        self::annotateAndValidateModel($actionContext->modelState);

        // execute action
        try {
            $actionResult = self::executeAction($actionContext);
        }
        catch (\Exception $ex) {
            // add the error to the modelState
            $actionContext->modelState->addError('.', $ex);
            $actionResult = null;
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
            if ($actionResult instanceof ViewResult) {
                // create view data
                $viewData = $actionContext->modelState->getKeyValuePair();

                $viewDataProperty = new \ReflectionProperty('\PhpMvc\Controller', 'viewData');
                $viewDataProperty->setAccessible(true);
                $controllerViewData = $viewDataProperty->getValue($actionContext->controller);

                if (!empty($controllerViewData)) {
                    $viewData = array_unique(array_merge($viewData, $controllerViewData), \SORT_STRING);
                }

                // create instance of view context
                $viewContext = new ViewContext($actionContext, $actionResult, $viewData);

                // get path of view file
                $viewContext->viewFile = PathUtility::getViewFilePath(PHPMVC_CURRENT_VIEW_PATH);
    
                // set view context
                $viewContextProperty = new \ReflectionProperty('\PhpMvc\View', 'viewContext');
                $viewContextProperty->setAccessible(true);
                $viewContextProperty->setValue(null, $viewContext);
    
                $viewContextProperty = new \ReflectionProperty('\PhpMvc\Html', 'viewContext');
                $viewContextProperty->setAccessible(true);
                $viewContextProperty->setValue(null, $viewContext);

                // execute result with view context
                $actionResult->execute($viewContext);

                // get view
                if ($viewContext->viewFile === false) {
                    throw new ViewNotFoundException(PHPMVC_CURRENT_VIEW_PATH); // TODO
                }

                $viewContext->content = self::getView($viewContext->viewFile);

                // get layout
                if (!empty($viewContext->layout)) {
                    if (($layoutFile = PathUtility::getViewFilePath($viewContext->layout)) !== false) {
                        $result = self::getView($layoutFile);
                    }
                    else {
                        throw new ViewNotFoundException($viewContext->layout);
                    }
                }
            }
            elseif ($actionResult instanceof ActionResult) {
                // execute result with action context
                $actionResult->execute($actionContext);
            }
        }

        // render
        self::$response->write($result);
        self::$response->end();
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
        $modelState->annotations = $actionContext->modelState->annotations;
        $hasModel = false;

        // get http params
        // TODO: подумать о возможности управлять этим
        $get = self::$request->getGetData();

        // change case of keys
        $get = array_change_key_case($get, CASE_LOWER);

        // post params
        $isPost = self::$request->isPost();
        $postData = $isPost ? self::$request->getPostData() : null;
        $post =  null;

        if (!empty($postData)) {
            $post = self::arrayToObject($postData);
        }
        
        if (empty($post) && $isPost) {
            $contentType = self::$request->getContentType();

            if (strrpos($contentType, '/json') !== false) {
                $requestBody = file_get_contents('php://input');

                $post = json_decode($requestBody);
            }
            else {
                // TODO...
            }
        }

        // get params of action method
        $r = new \ReflectionMethod(get_class($actionContext->controller), $actionContext->actionName);

        $methodParams = $r->getParameters();

        // search for the necessary parameters
        foreach ($methodParams as $param) {
            $name = strtolower($param->getName());

            if (!isset($get[$name]))
            {
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

                if ($param->isOptional())
                {
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
            else {
                $modelState[$name] = new ModelStateEntry($name, $arguments[$name] = $get[$name]);    
            }
        }

        $actionContext->arguments = $arguments;
        $actionContext->modelState = $modelState;
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

            if (!empty($dataAnnotation->name)) {
                $displayName = $dataAnnotation->name;
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
                            if (!empty($dataAnnotation2->name)) {
                                $displayName2 = $dataAnnotation2->name;
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
     * Converts array to object,
     * 
     * @param array $array The array to convert.
     * @param \stdClass $parent The parent object.
     * 
     * @return \stdClass
     */
    private static function arrayToObject($array, $parent = null) {
        $result = isset($parent) ? $parent : new \stdClass();
        
        ksort($array);

        foreach ($array as $key => $value)
        {
            if (strpos($key, '_') === false) {
                $result->$key = $value;
            } else {
                $name = explode('_', $key);
                $newKey = array_shift($name);

                if (!isset($result->$newKey)) {
                    $result->$newKey = new \stdClass();
                }
                
                self::arrayToObject(array($name[0] => $value), $result->$newKey);
            }
        }

        return $result;
    }

    /**
     * Calls the action method and returns the result.
     * 
     * @return mixed
     */
    private static function executeAction($actionContext) {
        $action = new \ReflectionMethod($actionContext->controller, $actionContext->actionName);

        if (!$action->isPublic()) {
            throw new \Exception('Action methods must have a public modifier. The action call "' . $actionContext->actionName . '" is denied.');
        }

        return $action->invokeArgs($actionContext->controller, $actionContext->arguments);
    }

    /**
     * Gets the view.
     * 
     * @param string $path File name or path to the view file.
     * 
     * @return string
     */
    public static function getView($path) {
        if ($path === false) {
            throw new \Exception('The view file is not specified. Probably the correct path to the file was not found. Make sure that all paths are specified correctly, the files exists and is available.');
        }

        ob_start();

        require($path);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

}