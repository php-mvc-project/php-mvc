<?php
namespace PhpMvc;

/**
 * Represents a collection of routes and provides a route search for the request context.
 */
class RouteCollection {

    /**
     * List of routes.
     * 
     * @var Route[]
     */
    private $routes = array();

    /**
     * Adds a route to collection.
     * 
     * @param Route $route The route to add.
     * 
     * @return void
     */
    public function add($route) {
        $this->validateRoute($route);
        $this->routes[] = $route;
    }

    /**
     * Remove all routes.
     * 
     * @return void
     */
    public function clear() {
        $this->routes = array();
    }

    /**
     * Returns the first route similar to the current url.
     * 
     * @param HttpContextBase $httpContext Context of the request.
     * 
     * @return Route|null
     */
    public function getRoute($httpContext) {
        $csm = false ? '' : 'i';
        $path = trim($httpContext->getRequest()->getRequestUri(), '/');
        
        if (($qsIndex = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $qsIndex);
        }

        if ($path == 'index.php') {
            $path = '';
        }

        foreach($this->routes as $route) {
            // required parameters
            $required = $route->defaults;

            // escape special characters
            $safeTemplate = $this->escapeTemplate($route->template);

            // make patterns for each path segments
            $segments = $this->extractSegments($route, $safeTemplate, $required);

            // make final pattern
            $pattern = '';

            foreach ($segments as $segment) {
                if (!empty($segment['before'])) {
                    $pattern .= $segment['before'];
                }

                $pattern .= $segment['pattern'];

                if (!empty($segment['after'])) {
                    $pattern .= $segment['after'];
                }

                if (empty($segment['glued'])) {
                    if ($segment['optional'] || !empty($segment['end']) || !empty($segment['pre-end'])) {
                        $pattern .= '(\/|)';
                    }
                    else {
                        $pattern .= '\/';
                    }
                }
            }

            // test url
            if (preg_match('/^' . $pattern . '$/s' . $csm, $path, $matches) === 1) {
                // match is found
                $result = clone $route;
                $values = array();

                foreach ($segments as $segment) {
                    $name = $segment['name'];

                    if (!empty($matches[$name])) {
                        if (empty($values[$name])) {
                            $values[$name] = $matches[$name];
                        }
                    }

                    if (!empty($segment['default'])) {
                        if (empty($values[$name]) && $segment['default'] != UrlParameter::OPTIONAL) {
                            $values[$name] = $segment['default'];
                        }
                    }
                }

                if (!empty($route->defaults)) {
                    foreach ($route->defaults as $name => $defaultValue) {
                        if (empty($values[$name])) {
                            $values[$name] = $defaultValue;
                        }
                    }
                }

                if (true) {
                    $values = array_map('strtolower', $values);
                }

                $result->values = $values;

                return $result;
            }
        }

        return null;
    }
    
    /**
     * Quote regular expression characters.
     * 
     * @return string
     */
    private function escapeTemplate($value) {
        return preg_replace_callback('/([^\{\/\x5c]+|)(\{[^\}]+\})([^\{\/\x5c]+|)/', function($m) {
            array_shift($m);

            $m = array_filter($m, function($item) {
                return !empty($item);
            });

            $m = array_map(function($item) {
                return mb_substr($item, 0, 1) !== '{' ? preg_quote($item, '/') : $item;
            }, $m);

            return implode('', $m);
        }, $value);
    }

    /**
     * Parses segments of route.
     * 
     * @param Route $route The route instance.
     * @param string $template The template to parse.
     * @param array &$required Required parameters.
     * 
     * @return array
     */
    private function extractSegments($route, $template, &$required) {
        $required = $route->defaults;
        $segments = array();
        $names = array();

        if (preg_match_all('/\{([^\}]+)\}/', $template, $matches, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE)) {
            $prevMatchString = $prevName = '';
            $prevIndex = 0;

            for ($i = 0, $count = count($matches); $i < $count; ++$i) {
                $matchString = $matches[$i][0][0];
                $name  = $matches[$i][1][0];
                $index = $matches[$i][0][1];
                $before = $prevAfter = '';
                $prevGlued = false;
                $segmentPattern = '';
                $optional = false;
                $default = null;
                
                // parse name
                if (count($name = explode('=', $name)) > 1) {
                    $default = $name[1];
                    $name = $name[0];
                }
                else {
                    $name = $name[0];
                }

                // check optional marker
                if (count($name = explode('?', $name)) > 1) {
                    $default = UrlParameter::OPTIONAL;
                    $name = $name[0];
                }
                else {
                    $name = $name[0];
                }

                // default value is allowed or not
                if (!empty($default) && !empty($route->defaults[$name])) {
                    throw new \Exception('The route parameter "' . $name . '" has both an inline default value and an explicit default value specified. A route parameter must not contain an inline default value when a default value is specified explicitly. Consider removing one of them.');
                }
                elseif (empty($default) && !empty($route->defaults[$name])) {
                    $default = $route->defaults[$name];
                }

                // check uniqueness
                if (in_array($name, $names)) {
                    trigger_error(
                        'The route "' . $route->name . '" contains more than one parameter named "' . 
                        $name . '". Route parsing may not work correctly. ' .
                        'Please, try not to use elements with the same name.', 
                        E_USER_WARNING
                    );

                    continue;
                }

                // before and after text
                if ($index > $prevIndex + ($prevLen = mb_strlen($prevMatchString))) {
                    $before = mb_substr($template, $prevIndex + $prevLen, $index - $prevIndex - $prevLen);
                }

                $prevGlued = !empty($before) && $before[0] != '/';

                if (count($parts = explode('/', $before)) > 1) {
                    $prevAfter = $parts[0];
                    $before = $parts[1];
                }

                if ($i > 0) {
                    if (!empty($prevAfter)) {
                        $segments[$i - 1]['after'] = $prevAfter;
                    }
    
                    if ($prevGlued) {
                        $segments[$i - 1]['glued'] = true;
                    }
                }

                // make pattern
                if (!empty($route->constraints[$name])) {
                    if (!empty($default)) {
                        $segmentPattern = '(?<' . $name . '>(' . $route->constraints[$name] . ')|)';
                        $optional = true;
                    }
                    else {
                        $segmentPattern = '(?<' . $name . '>' . $route->constraints[$name] . ')';
                    }
                }
                else {
                    if (!empty($default)) {
                        $segmentPattern = '(?<' . $name . '>[^\/]*)';
                        $optional = true;
                    }
                    else {
                        $segmentPattern = '(?<' . $name . '>[^\/]+)';
                    }
                }

                // result
                $segment = array(
                    'name' => $name,
                    'pattern' => $segmentPattern,
                    'optional' => $optional,
                    'default' => $default,
                );
                
                if (!empty($before)) {
                    $segment['before'] = $before;
                }

                $segments[] = $segment;
                $names[] = $name;
                
                $prevName = $name;
                $prevIndex = $index;
                $prevMatchString = $matchString;
            }
        }
        else {
            $segments[] = array(
                'pattern' => $template,
                'optional' => false,
            );
        }

        $this->setBounds($segments);

        return $segments;
    }

    /**
     * Marks the boundaries.
     * 
     * @param array &$segments Segments to processing.
     * 
     * @return void
     */
    private function setBounds(&$segments) {
        for ($i = 0, $count = count($segments); $i < $count; ++$i) {
            if ($segments[$i]['optional'] && $i - 1 > 0) {
                $optional = true;

                for ($j = $i; $j < $count; ++$j) {
                    if (!$segments[$j]['optional']) {
                        $optional = false;
                        break;
                    }
                }

                if ($optional) {
                    $segments[$i - 1]['pre-end'] = true;
                }
            }

            if ($i + 1 == $count) {
                $segments[$i]['end'] = true;
            }
        }
    }

    /**
     * Verifies that the data in the route instance is correct.
     * 
     * @param Route $route The route to check.
     * 
     * @return void
     */
    private function validateRoute($route) {
        if (!$route instanceof Route) {
            throw new \Exception('A type of Route or a derivative is expected.');
        }
        
        if (empty($route->name)) {
            throw new \Exception('The name of the route is expected. The value must not be empty.');
        }

        if (!$this->isUniqueRouteName($route->name)) {
            throw new \Exception('The name "' . $route->name . '" is already used for another route. Specify a unique name.');
        }

        if (empty($route->template)) {
            throw new \Exception('The route "' . $route->name . '" does not specify a template. Please specify a template.');
        }
    }

    /**
     * Checks the uniqueness of the route name.
     * 
     * @param string $name Name to check.
     * 
     * @return bool
     */
    private function isUniqueRouteName($name) {
        foreach ($this->routes as $route) {
            if (mb_strtolower($route->name) == mb_strtolower($name)) {
                return false;
            }
        }

        return true;
    }

}