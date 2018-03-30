<?php
namespace PhpMvc;

/**
 * The main class that works wonders.
 */
final class Make {

    /**
     * Magic!
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    public static function magic($appNamespace) {
        self::init($appNamespace);
        self::requestValidation();
        self::include();

        header('X-Powered-By', 'https://github.com/meet-aleksey/php-mvc');
        
        self::render();
    }

    /**
     * Performs the initialization of the engine.
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    private static function init($appNamespace) {
        define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        define('PHPMVC_ROOT_PATH', getcwd() . PHPMVC_DS);
        define('PHPMVC_CORE_PATH', __DIR__ .PHPMVC_DS);
        define('PHPMVC_CONFIG_PATH', PHPMVC_ROOT_PATH . 'config' . PHPMVC_DS);
        define('PHPMVC_CONTROLLER_PATH', PHPMVC_ROOT_PATH . 'controllers' . PHPMVC_DS);
        define('PHPMVC_MODEL_PATH', PHPMVC_ROOT_PATH . 'models' . PHPMVC_DS);
        define('PHPMVC_VIEW_PATH', PHPMVC_ROOT_PATH . 'views' . PHPMVC_DS);
        define('PHPMVC_SHARED_PATH', PHPMVC_VIEW_PATH . 'shared' . PHPMVC_DS);
        define('PHPMVC_UPLOAD_PATH', PHPMVC_ROOT_PATH . 'upload' . PHPMVC_DS);
        
        define('PHPMVC_APP_NAMESPACE', $appNamespace);

        define('PHPMVC_CONTROLLER', isset($_REQUEST['controller']) ? ucfirst($_REQUEST['controller']) : 'Home');
        define('PHPMVC_ACTION', isset($_REQUEST['action']) ? lcfirst($_REQUEST['action']) : 'index');
        define('PHPMVC_VIEW', strtolower(PHPMVC_CONTROLLER));
        define('PHPMVC_CURRENT_CONTROLLER_PATH', PHPMVC_CONTROLLER_PATH . PHPMVC_VIEW . PHPMVC_DS);
        define('PHPMVC_CURRENT_VIEW_PATH', PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . PHPMVC_ACTION . '.php');
    }

    private static function requestValidation() {
        if (substr(PHPMVC_ACTION, 0, 2) == '__') {
            throw new \Exception('Action names can not begin with a "_".');
        }
    }

    /**
     * Includes the required files. 
     */
    private static function include() {
        require_once PHPMVC_CORE_PATH . 'Loader.php';
        require_once PHPMVC_CORE_PATH . 'Controller.php';
    }

    private static function render() {
        $result = null;
        
        $actionContext = new ActionContext();

        ViewContext::$actionContext = $actionContext;

        // create instance of controller
        $controllerName = PHPMVC_APP_NAMESPACE . '\\Controllers\\' . PHPMVC_CONTROLLER . 'Controller';
        $actionContext->controller = new $controllerName();
        $actionContext->actionName = PHPMVC_ACTION;
        
        // arguments and model state
        self::makeActionState($actionContext);

        ViewContext::$modelState = $actionContext->modelState;

        // execute action
        try {
            $actionResult = self::executeAction($actionContext);
        }
        catch (\Exception $ex) {
            // add the error to the modelState
            $actionContext->modelState->addError('.', $ex);
            $actionResult = null;
        }

        // annotation and validation
        self::annotateAndValidateModel($actionContext->modelState);

        // execute result
        if (isset($actionResult) && $actionResult instanceof ActionResult) {
            $actionResult->execute($actionContext);
        }
        
        ViewContext::$actionResult = $actionResult;

        // append model state keys to view data
        ViewContext::$viewData = array_merge(ViewContext::$viewData, $actionContext->modelState->getKeyValuePair());

        // get view
        if ((ViewContext::$viewFile = self::getViewFilePath(PHPMVC_CURRENT_VIEW_PATH)) !== false) {
            ViewContext::$content = self::getView(ViewContext::$viewFile);
        }
        else {
            ViewContext::$content = ViewContext::$actionResult;
        }

        // get layout
        if (!empty(ViewContext::$layout)) {
            $result = self::getView(self::getViewFilePath(ViewContext::$layout));
        }
        else {
            $result = ViewContext::$content;
        }

        // preparation for render
        if (!is_string($result)) {
            if (($result = json_encode(ViewContext::$actionResult)) === false) {
                throw new \ErrorException('JSON encode error #' . json_last_error() . ': ' . json_last_error_msg());
            }

            header('Content-Type: application/json');
        }

        // render
        echo $result;
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
        $hasModel = false;

        // get http params
        // TODO: подумать о возможности управлять этим
        $get = $_GET;

        // change case of keys
        $get = array_change_key_case($get, CASE_LOWER);

        // post params
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $post =  null;

        if (!empty($_POST)) {
            $postData = $_POST;
            $post = self::arrayToObject($postData);
        }

        if (empty($post) && $isPost) {
            $contentType = isset($_SERVER['HTTP_CONTENT_TYPE']) ? $_SERVER['HTTP_CONTENT_TYPE'] : null;
            $contentType = empty($contentType) && isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;
            $contentType = !empty($contentType) ? $contentType : '';

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
                    $arguments[$name] = $post;
                    
                    // parse post data
                    foreach (get_object_vars($post) as $key => $value) {
                        if ($value !== null) {
                            $modelState[$key] = new ModelStateEntry($key, $value);
                        }
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

        if (($dataAnnotation = ViewContext::getModelDataAnnotation($key)) !== null) {
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
                    $modelState->addError($key, $displayName . ' is required. Value cannot be empty.');
                }
            }

            // compareWith
            if (!empty($dataAnnotation->compareWith)) {
                if (!empty($entry2 = $modelState[$dataAnnotation->compareWith[0]])) {
                    if (!($entry->validationState = ($entry->value == $entry2->value))) {
                        $displayName2 = $entry2->key;

                        if (($dataAnnotation2 = ViewContext::getModelDataAnnotation($entry2->key)) !== null) {
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

    public static function getView($path) {
        ob_start();

        require($path);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    private static function getRequestModel() {
        // TODO: GET and other:
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return NULL;
        }

        return (object)$_POST;
    }

    /**
     * Searches for a file with a specified name and returns the correct path.
     * If the file is not found, returns FALSE. 
     * 
     * @param string $path The file name or file path.
     * @return string|bool
     */
    public static function getViewFilePath($path) {
        if (empty($path)) {
            return false;
        }
        elseif (is_file($result = $path)) {
            return $result;
        }
        elseif (is_file($result = PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . $path)) {
            return $result;
        }
        elseif (is_file($result = PHPMVC_SHARED_PATH . $path)) {
            return $result;
        }

        if (strlen($path) > 4 && substr($path, -4) != '.php') {
            return self::getViewFilePath($path . '.php');
        }

        return false;
    }
    
}