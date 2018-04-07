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
     * 
     * @param ActionContext $actionContext The current context of the action.
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * 
     * @return string
     */
    public static function action($actionContext, $actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        if (empty($actionContext) || !$actionContext instanceof ActionContext) {
            throw new \Exception('The $actionContext is requred and type must be derived from "\PhpMvc\ActionContext".');
        }

        if (empty($actionName)) {
            $modelState->addError($key, '$actionName is required. Value must not be empty.');
        }

        $routeValues = isset($routeValues) ? $routeValues : array();

        $result = '';

        if (empty($controllerName)) {
            // use current route
            $route = $actionContext->route;
        }
        else {
            // search route
            $routes = $actionContext->httpContext->getRoutes();

            foreach ($routes as $route) {
                if (empty($route->defaults)) {
                    continue;
                }

                if (!empty($route->defaults['controller']) && $route->defaults['controller'] == $controllerName) {
                    break;
                }
            }
        }

        $segments = $route->getSegments();

        foreach ($segments as $segment) {
            $result .= $segment->after;

            if (empty($segment->name)) {
                $result .= $segment->pattern;
            }
            elseif ($segment->name == 'controller' && !empty($controllerName)) {
                $result .= $controllerName;
            }
            elseif ($segment->name == 'action') {
                $result .= $actionName;
            }
            else {
                if (!empty($routeValues[$segment->name])) {
                    $result .= $routeValues[$segment->name];
                    unset($routeValues[$segment->name]);
                }
                else 
                {
                    if ($segment->default !== UrlParameter::OPTIONAL) {
                        $result .= $segment->default;
                    }
                }
            }

            $result .= $segment->before;

            $result .= '/';
        }

        $result = rtrim($result, '/');

        if (!empty($routeValues)) {
            $result .= '?' . http_build_query($routeValues);
        }

        if (!empty($fragment)) {
            $result .= '#' . $fragment;
        }

        // TODO: remove index
        // TODO: lowercase

        return $result;
    }

}