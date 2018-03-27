<?php
namespace PhpMvc;

class Make {
    
    /**
     * Magic!
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    public static function magic($appNamespace) {
        self::init($appNamespace);
        self::include();
        self::dispatch();
    }

    /**
     * Performs the initialization of the engine.
     * 
     * @param string $appNamespace Root namespace of the application.
     */
    private static function init($appNamespace) {
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT_PATH', getcwd() . DS);
        define('CORE_PATH', ROOT_PATH . 'core' . DS);
        define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
        define('CONTROLLER_PATH', ROOT_PATH . 'controllers' . DS);
        define('MODEL_PATH', ROOT_PATH . 'models' . DS);
        define('VIEW_PATH', ROOT_PATH . 'views' . DS);
        define('SHARED_PATH', VIEW_PATH . 'shared' . DS);
        define('UPLOAD_PATH', ROOT_PATH . 'upload' . DS);
        
        define('APP_NAMESPACE', $appNamespace);

        define('CONTROLLER', isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'Home');
        define('ACTION', isset($_REQUEST['action']) ? $_REQUEST['action'] : 'index');
        define('VIEW', strtolower(CONTROLLER));
        define('CURRENT_CONTROLLER_PATH', CONTROLLER_PATH . VIEW . DS);
        define('CURRENT_VIEW_PATH', VIEW_PATH . VIEW . DS . ACTION . '.php');
    }

    private static function include() {
        require_once __DIR__ . DS . 'Loader.php';
        // require_once CORE_PATH . 'Controller.php';
    }

    private static function dispatch() {
        $controller_name = APP_NAMESPACE . '\\Controllers\\' . CONTROLLER . 'Controller';
        $controllerInstance = new $controller_name();
        $actionName = ACTION;
        $requestModel = self::getRequestModel();
        // TODO: object
        $layout = NULL;
        $title = NULL;
        $viewContent = NULL;
        $result = NULL;
        $model = NULL;
        $modelState = NULL;

        try {
            $model = $controllerInstance->$actionName($requestModel);
        }
        catch (\Exception $ex) {
            // TODO: 500
            // TODO: object
            $modelState = array(
                'exception' => $ex
            );
        }

        // extract view settings
        if (is_file(CURRENT_VIEW_PATH) && !empty($viewHeaders = self::getViewHeaders())) {
            if (preg_match('/^\s*array\([^\)]+\);/m', $viewHeaders, $m))
            {
                eval('$view_settings = ' . $m[0]);
                extract($view_settings, EXTR_PREFIX_ALL, 'view');

                // TODO: the prefix may be superfluous, think about it.
                if (isset($view_layout)) {
                    $layout = $view_layout;
                }

                if (isset($view_title)) {
                    $title = $view_title;
                }
            }
        }

        // get content
        if (is_file(CURRENT_VIEW_PATH)) {
            $viewContent = self::getContent(CURRENT_VIEW_PATH, $model, $modelState);
        }

        // get layout
        if (!empty($layout)) {
            $layoutData = array(
                'main' => $viewContent, 
                'title' => $title
            );
            // TODO: seach file
            $result = self::getLayout(SHARED_PATH . $layout, $layoutData);
        } else {
            $result = $viewContent;
        }

        // render
        echo $result;
    }

    private static function getViewHeaders() {
        // TODO: cache
        $viewFile = fopen(CURRENT_VIEW_PATH, 'r');
        $viewHeaders = '';

        while (!feof($viewFile)) {
            $line = fgets($viewFile);
            $viewHeaders .= $line;

            if (strpos($line, '?>') !== FALSE) {
                break;
            }
        }

        fclose($viewFile);

        return $viewHeaders;
    }

    private static function getContent($filePath, $data = NULL, $modelState = NULL) {
        ob_start();

        $inject = function(&$model) use($data) {
            if (!empty($data)) {
                $model = $data;
            }
        };

        // TODO: может быть лучше сделать класс и передавать все приблуды по ссылке.
        // Что-нибудь, типа $html->route(...), $html->inject($model) и т.п.
        $route = function($actionName, $controllerName = NULL) {
            $params = '';

            if (empty($controllerName)) {
                $controllerName = VIEW;
            }
            elseif (is_array($controllerName)) {
                $params = '&' . http_build_query($controllerName);
                $controllerName = VIEW;
            }

            // TODO: url mode
            return '/?controller=' . $controllerName . '&action=' . $actionName . $params;
            // return '/' . $controllerName . '/' . $actionName;
        };

        $content = function($name = NULL, $contentModel = NULL) use($data) {
            if (!isset($contentModel)) {
                $contentModel = $data;
            }

            return self::content($name, $contentModel);
        };

        require($filePath);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    private static function getLayout($filePath, $data = NULL) {
        ob_start();

        $content = function($name = NULL) use($data) {
            return self::content($name, $data);
        };

        require($filePath);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    private static function content($name = NULL, $data = NULL) {
        if (empty($name)) {
            $name = 'main';
        }

        if (isset($data) && is_array($data) && isset($data[$name])) {
            echo $data[$name];
        }
        elseif (is_file(VIEW_PATH . VIEW . DS . $name)) {
            echo self::getContent(VIEW_PATH . VIEW . DS . $name, $data);
        }
        elseif (is_file(SHARED_PATH . $name)) {
            echo self::getContent(SHARED_PATH . $name, $data);
        }
        elseif (is_file($name)) {
            echo self::getContent($name, $data);
        }
        else {
            // TODO:
            // echo '<pre>Content block with name "' . $name . '" not found</pre>';
        }
    }

    private static function getRequestModel() {
        // TODO: GET and other:
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return NULL;
        }

        return (object)$_POST;
    }
    
}