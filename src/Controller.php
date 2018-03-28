<?php
namespace PhpMvc;

/**
 * Base controller.
 */
class Controller {
   
    public function __construct() {
    
    }

    /**
     * Creates a ViewResult object that renders a view to the response.
     * 
     * @param mixed $model Model.
     * @param string $layout Layout file name or path.
     * 
     * @return ViewResult
     */
    public function view($model = null, $layout = null) {
        return new ViewResult($model, $layout);
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
    public function json($data, $options = 0, $depth = 512) {
        return new JsonResult($data, $options, $depth);
    }

    /**
     * Creates a FileResult object by using the file contents and file type.
     * 
     * @param string $path The file path to output.
     * @param string $contentType The content type.
     * @param string $downloadName the content-disposition header so that a file-download dialog box is displayed in the browser with the specified file name.
     * 
     * @return FileResult
     */
    public function file($path, $contentType = null, $downloadName = null) {
        return new FileResult($path, $contentType, $downloadName);
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