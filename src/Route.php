<?php
namespace PhpMvc;

/**
 * Represents a route.
 */
class Route {

    /**
     * Gets or sets sample of the URL on which the comparison was made.
     * 
     * @var string|null
     */
    protected $url;

    /**
     * Gets or sets unique route name.
     * 
     * @var string
     */
    public $name;

    /**
     * Gets or sets route template.
     * For example: {controller=Home}/{action=index}/{id?}
     * 
     * @var string
     */
    public $template;

    /**
     * Gets or sets default value of the route segments.
     * 
     * @var array
     */
    public $defaults;

    /**
     * Gets or sets route parse constraints.
     * 
     * @var array
     */
    public $constraints;

    /**
     * Gets or sets route values.
     * 
     * @var array
     */
    public $values;

    /**
     * Gets or sets segments of the route.
     * 
     * @var RouteSegment[]
     */
    private $segments;

    /**
     * Initializes a new instance of the ActionContext for the current request.
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
     */
    public function __construct($name = null, $template = null, $defaults = null, $constraints = null) {
        $this->name = $name;
        $this->template = $template;
        $this->defaults = $defaults;
        $this->constraints = $constraints;
    }

    /**
     * Get value or default.
     * 
     * @param string $key The key whose value is to try to get.
     * @param string $default Default value. Only if the value in the $defaults property is not found.
     * 
     * @return string
     */
    public function getValueOrDefault($key, $default = null) {
        if (!empty($this->values[$key])) {
            return $this->values[$key];
        }
        elseif (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }
        else {
            return $default;
        }
    }

    /**
     * Returns segments of the route.
     * 
     * @var RouteSegment[]
     */
    public function getSegments() {
        if (!empty($this->segments)) {
            return $this->segments;
        }

        // required parameters
        $required = $this->defaults;

        // escape special characters
        $safeTemplate = $this->escapeTemplate($this->template);

        // extract segments
        $this->segments = $this->extractSegments($this, $safeTemplate, $required);

        return $this->segments;
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
     * @return RouteSegment[]
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

                if (!empty($prevAfter)) {
                    if ($i > 0) {
                        $segments[$i - 1]->after = $prevAfter;
                    }
                    else {
                        $first = new RouteSegment();
                        $first->pattern = $prevAfter;
                        $first->optional = false;

                        $segments[] = $first;
                    }
                }

                if ($prevGlued && $i > 0) {
                    $segments[$i - 1]->glued = true;
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
                $segment = new RouteSegment();
                $segment->name = $name;
                $segment->pattern = $segmentPattern;
                $segment->optional = $optional;
                $segment->default = $default;

                if (!empty($before)) {
                    $segment->before = $before;
                }

                $segments[] = $segment;
                $names[] = $name;

                $prevName = $name;
                $prevIndex = $index;
                $prevMatchString = $matchString;
            }
        }
        else {
            $segment = new RouteSegment();
            $segment->pattern = $template;
            $segment->optional = false;

            $segments[] = $segment;
        }

        $this->setBounds($segments);

        return $segments;
    }

    /**
     * Marks the boundaries.
     * 
     * @param RouteSegment[] &$segments Segments to processing.
     * 
     * @return void
     */
    private function setBounds(&$segments) {
        for ($i = 0, $count = count($segments); $i < $count; ++$i) {
            if ($segments[$i]->optional && $i - 1 > 0) {
                $optional = true;

                for ($j = $i; $j < $count; ++$j) {
                    if (!$segments[$j]->optional) {
                        $optional = false;
                        break;
                    }
                }

                if ($optional) {
                    $segments[$i - 1]->preEnd = true;
                }
            }

            if ($i + 1 == $count) {
                $segments[$i]->end = true;
            }
        }
    }

}