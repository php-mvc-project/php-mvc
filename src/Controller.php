<?php
namespace PhpMvc;

/**
 * Base controller.
 */
class Controller {
   
    public function __construct() {
    
    }

    /**
     * 
     * @return View
     */
    public function view($model = null, $layout = null) {
        return new View($model, $layout);
    }

    /**
     * @return FileResult
     */
    public function file($path, $contentType = null, $downloadName = null) {
        $result = new FileResult();
        $result->path = $path;
        $result->contentType = $contentType;
        $result->downloadName = $downloadName;

        return $result;
    }

    public function error($message, $model = null) {
        throw new \Exception($message);
    }

    /**
     * Returns TRUE if the request is POST.
     * 
     * @return bool
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function redirect($actionName, $controllerName = null) {
        // TODO: global helper method for route builder.
        if (empty($controllerName)) {
            $controllerName = VIEW;
        }

        header('Location: /?controller=' . $controllerName . '&action=' . $actionName);
        // header('Location: /' . $controllerName . '/' . $actionName);
        exit;
    }

}