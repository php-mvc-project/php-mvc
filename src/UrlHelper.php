<?php
namespace PhpMvc;

/**
 * Helper class for building URI.
 */
final class UrlHelper {

    /**
     * Generates a URL with an absolute path for an action method, which contains the
     * action name, controller name, route values, protocol to use, host name, and fragment.
     * Generates an absolute URL if $schema and $host are non-null.
     */
    public static function action($actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        $result = '';

        $actionName = !empty($actionName) ? $actionName : PHPMVC_ACTION;
        $controllerName = !empty($controllerName) ? $controllerName : PHPMVC_VIEW;

        $controllerName = '/?controller=' . $controllerName . '&action=' . $actionName;
        
        return $result;
    }

}