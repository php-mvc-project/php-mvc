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

        $httpContext = $actionContext->httpContext;
        $options = $httpContext->getRouteOptions();
        $result = '';

        if (!empty($schema)) {
            $result .= $schema . '://';

            if (empty($host)) {
                $server = $httpContext->getRequest()->serverVariables();
                $result .= $server['HTTP_HOST'] . '/';
            }
        }

        if (!empty($host)) {
            if (empty($schema)) {
                if ($httpContext->getRequest()->isSecureConnection()) {
                    $schema = 'https';
                }
                else {
                    $schema = 'http';
                }

                $result .= $schema . '://';
            }

            $result .= $host . '/';
        }

        if (empty($result)) {
            $result = '/';
        }

        $routeValues = isset($routeValues) ? $routeValues : array();

        if (empty($controllerName)) {
            // use current route
            $route = $actionContext->route;
        }
        else {
            // search route
            $routes = $httpContext->getRoutes();

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
        $last = null;

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

            if ($segment->preEnd === true || ($segment->end === true && $segment->default !== UrlParameter::OPTIONAL)) {
                $last = $segment;
            }
        }

        $result = rtrim($result, '/');

        if ($options->removeLastSegmentIfValueIsDefault === true) {
            $lastSegmentPos = strrpos($result, '/');
            $lastSegment = trim(mb_substr($result, $lastSegmentPos), '/');

            if (empty($last->before) && !empty($last->default) && mb_strtolower($last->default) === mb_strtolower($lastSegment)) {
                $result = mb_substr($result, 0, $lastSegmentPos);
            }
        }

        if ($options->lowercaseUrls === true) {
            $result = mb_strtolower($result);
        }

        if ($options->appendTrailingSlash === true) {
            $result .= '/';
        }

        if (!empty($routeValues)) {
            $result .= '?' . http_build_query($routeValues);
        }

        if (!empty($fragment)) {
            $result .= '#' . $fragment;
        }

        return $result;
    }

}