<?php
namespace PhpMvc;

/**
 * Helper class for HTML.
 */
class Html {

    /**
     * Test function.
     */
    public static function test($value) {
        return $value;
    }

    public static function getTitle($default) {
        if (empty(ViewContext::$title)) {
            return $default;
        }

        return ViewContext::$title;
    }

    public static function renderBody() {
        // echo self::view(PHPMVC_CURRENT_VIEW_PATH);
        echo ViewContext::$content;
    }

    public static function render($path) {
        echo self::view($path);
    }

    public static function view($path) {
        if (($viewPath = Make::getViewFilePath($path)) !== false) {
            return Make::getView($viewPath);
        }
        else {
            throw new ViewNotFoundException($path);
        }
    }

    /**
     * Returns the path to action.
     * 
     * @return string
     */
    public static function action($actionName, $controllerName = null) {
        $params = '';

        if (empty($controllerName)) {
            $controllerName = PHPMVC_VIEW;
        }
        elseif (is_array($controllerName)) {
            $params = '&' . http_build_query($controllerName);
            $controllerName = PHPMVC_VIEW;
        }

        // TODO: url mode
        return '/?controller=' . $controllerName . '&action=' . $actionName . $params;
        // return '/' . $controllerName . '/' . $actionName;
    }

}