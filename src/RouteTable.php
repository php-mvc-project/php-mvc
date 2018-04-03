<?php
namespace PhpMvc;

/**
 * Stores the URL routes for an application.
 */
class RouteTable {

    /**
     * Gets or sets a collection of routes.
     * 
     * @var Route[]
     */
    private static $routes = array();

    /**
     * Indicates whether the case of characters should be case-sensitive in determining the URL or not.
     * Default: false (case insensitive).
     * 
     * @var bool
     */
    private static $caseSensitive = false;

    /**
     * Sets the case sensitivity mode when searching for URL.
     * 
     * @param bool $value
     * 
     * @return void
     */
    public static function setCaseSensitive($value) {
        RouteTable::$caseSensitive = $value;
    }

    /**
     * Adds a rule.
     * 
     * @param Route|string $route A rule to add. 
     * 
     * @return void
     */
    public static function add($route) {
        self::validateRoute($route);
        self::$routes[] = $route;
    }

    /**
     * Adds a rule.
     * 
     * @param string $name The unique name of the route.
     * @param string $template The template by which the route will be searched.
     * Use curly braces to denote the elements of the route.
     * For example: {controller}/{action}/{id}
     * {controller=Home}/{action=index}/{id?}
     * @param array $defaults An associative array containing the default values for the elements defined in the $template.
     * For example, $template is {controller}/{action}/{id}
     * $defaults = array('controller' => 'Home', 'action' => 'index', id => \PhpMvc\UrlParameter.OPTIONAL)
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * For example, $template is {controller}/{action}/{id}
     * $constraints = array('id' => '\w+')
     * 
     * @return void
     */
    public static function addRoute($name, $template, $defaults = null, $constraints = null) {
        $route = new Route();

        $route->name = $name;
        $route->template = $template;
        $route->defaults = $defaults;
        $route->constraints = $constraints;

        self::validateRoute($route);

        self::$routes[] = $route;
    }

    public static function ignoreRoute() {

    }

    /**
     * Remove all routes.
     * 
     * @return void
     */
    public static function clear() {
        self::$routes = array();
    }

    /**
     * Returns the first route similar to the current url.
     * 
     * @return Route|null
     */
    public static function getRoute() {
        $csm = RouteTable::$caseSensitive ? '' : 'i';
        $path = trim(ViewContext::$requestContext->getRequestUri(), '/');
        
        if (($qsIndex = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $qsIndex);
        }

        if ($path == 'index.php') {
            $path = '';
        }

        foreach(self::$routes as $route) {
            // required parameters
            $required = $route->defaults;

            // escape special characters
            $safeTemplate = self::escapeTemplate($route->template);

            // make patterns for each path segments
            $segments = self::extractSegments($route, $safeTemplate, $required);

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

                if (!RouteTable::$caseSensitive) {
                    $values = array_map('strtolower', $values);
                }

                $result->values = $values;

                return $result;
            }
        }

        return null;
    }

    /**
     * Verifies that the data in the route instance is correct.
     * 
     * @param Route $route The route to check.
     * 
     * @return void
     */
    private static function validateRoute($route) {
        if (!$route instanceof Route) {
            throw new \Exception('A type of Route or a derivative is expected.');
        }
        
        if (empty($route->name)) {
            throw new \Exception('The name of the route is expected. The value cannot be empty.');
        }

        if (!self::isUniqueRouteName($route->name)) {
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
    private static function isUniqueRouteName($name) {
        foreach (self::$routes as $route) {
            if (mb_strtolower($route->name) == mb_strtolower($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Quote regular expression characters.
     * 
     * @return string
     */
    private static function escapeTemplate($value) {
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
     * Parses segments.
     * 
     * @param Route $route The route instance.
     * @param string $template The template to parse.
     * @param array &$required Required parameters.
     * 
     * @return array
     */
    private static function extractSegments($route, $template, &$required) {
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
                    throw new \Exception('The route parameter "' . $name . '" has both an inline default value and an explicit default value specified. A route parameter cannot contain an inline default value when a default value is specified explicitly. Consider removing one of them.');
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

        self::setBounds($segments);

        return $segments;
    }

    /**
     * Marks the boundaries.
     * 
     * @param array &$segments Segments to processing.
     * 
     * @return void
     */
    private static function setBounds(&$segments) {
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

}