<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
     * Indicates that this ignore rule.
     * 
     * @var bool
     */
    public $ignore;

    /**
     * Gets or sets segments of the route.
     * 
     * @var RouteSegment[]
     */
    private $segments;

    /**
     * Gets or sets named list of segments.
     * 
     * @var RouteSegment[]
     */
    private $segmentsNamed;

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
     * @param bool $ignore Indicates that this ignore rule. Default: false.
     */
    public function __construct($name = null, $template = null, $defaults = null, $constraints = null, $ignore = false) {
        $this->name = $name;
        $this->template = $template;
        $this->defaults = $defaults;
        $this->constraints = $constraints;
        $this->ignore = ($ignore === true);
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
     * @param bool $named Specify whether to return a named collection (true) or array (false, default).
     * 
     * @var RouteSegment[]
     */
    public function getSegments($named = false) {
        if (!empty($this->segments)) {
            return $named === true ? $this->segmentsNamed : $this->segments;
        }

        // required parameters
        $required = $this->defaults;

        // escape special characters
        $safeTemplate = $this->escapeTemplate($this->template);

        // extract segments
        $this->segments = $this->extractSegments($this, $safeTemplate, $required);

        return $named === true ? $this->segmentsNamed : $this->segments;
    }

    /**
     * Quote regular expression characters.
     * 
     * @return string
     */
    private function escapeTemplate($value) {
        $result = $value;

        $result = str_replace('{{', chr(1), $result);
        $result = str_replace('}}', chr(2), $result);

        $result = preg_replace_callback('/([^\{\/\x5c]+|)(\{[^\}]+\})([^\{\/\x5c]+|)/', function($m) {
            array_shift($m);

            $m = array_filter($m, function($item) {
                return !empty($item);
            });

            $m = array_map(function($item) {
                return mb_substr($item, 0, 1) !== '{' ? preg_quote($item, '/') : $item;
            }, $m);

            return implode('', $m);
        }, $result);

        $result = str_replace(chr(1), '\\{', $result);
        $result = str_replace(chr(2), '\\}', $result);

        return $result;
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
        $namedSegments = array();

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
                $catchAll = false;
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

                // is catch-all
                if (count($name = explode('*', $name)) > 1) {
                    $catchAll = true;
                    $name = $name[1];
                }
                else {
                    $name = $name[0];
                }

                if ($catchAll && $i != $count - 1) {
                    throw new RouteParsingException(
                        $this, 
                        'A catch-all parameter can only appear as the last segment of the route template. ' .
                        'Please check the route parameter "' . $name . '" in the route "' . $this->name . '".'
                    );
                }

                // default value is allowed or not
                if (!empty($default) && !empty($route->defaults[$name])) {
                    throw new RouteParsingException(
                        $this,
                        'The route parameter "' . $name . '" in the route "' . $this->name . '" ' .
                        'has both an inline default value and an explicit default value specified. ' .
                        'A route parameter must not contain an inline default value when a default value is specified explicitly. ' . 
                        'Consider removing one of them.'
                    );
                }
                elseif (empty($default) && !empty($route->defaults[$name])) {
                    $default = $route->defaults[$name];
                }
                
                if (!empty($default) && empty($route->defaults[$name])) {
                    $route->defaults[$name] = $default;
                }
        
                // check uniqueness
                if (!empty($namedSegments[$name])) {
                    trigger_error(
                        'The route "' . $route->name . '" contains more than one parameter named "' . 
                        $name . '". Route parsing may not work correctly. ' .
                        'Please, try not to use elements with the same name.', 
                        \E_USER_WARNING
                    );

                    continue;
                }

                // before and after text
                if ($index > $prevIndex + ($prevLen = mb_strlen($prevMatchString))) {
                    $before = mb_substr($template, $prevIndex + $prevLen, $index - $prevIndex - $prevLen);
                }

                $prevGlued = !empty($before) && $before[0] != '/';

                if (count($parts = explode('/', $before)) > 1) {
                    $partsCount = count($parts);
                    $prevAfter = array_slice($parts, 0, $partsCount - 1);
                    $before = $parts[$partsCount - 1];
                }

                if (!empty($prevAfter)) {
                    if ($i > 0) {
                        // TODO: widthout implode
                        $segments[$i - 1]->after = implode('\/', $prevAfter);
                    }
                    else {
                        foreach ($prevAfter as $prevAfterItem) {
                            $first = new RouteSegment();
                            $first->pattern = $prevAfterItem;
                            $first->optional = false;
    
                            $segments[] = $first;
                        }
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
                    if ($catchAll === true) {
                        $segmentPattern = '(?<' . $name . '>.*)';
                        $optional = true;
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
                $namedSegments[$name] = $segment;;

                $prevName = $name;
                $prevIndex = $index;
                $prevMatchString = $matchString;
            }

            // tail
            if ($prevIndex + ($prevLen = mb_strlen($prevMatchString)) < mb_strlen($template)) {
                $before = mb_substr($template, $prevIndex + $prevLen);

                if (!empty($before)) {
                    $last = new RouteSegment();
                    $last->pattern = ltrim($before, '/');
                    $last->optional = false;

                    $segments[] = $last;
                    $namedSegments[] = $last;
                }
            }
        }
        else {
            $items = explode('/', $template);

            foreach ($items as $item) {
                $segment = new RouteSegment();
                $segment->pattern = $item;
                $segment->optional = false;
    
                $segments[] = $segment;
            }

            $namedSegments = $segments;
        }

        $this->setBounds($segments);

        if ($this->ignore !== true && ((empty($namedSegments['controller']) && empty($this->defaults['controller'])) || (empty($namedSegments['action']) && empty($this->defaults['action'])))) {
            throw new RouteParsingException(
                $this,
                'The route "' . $this->name . '" must contain {controller} and {action}. ' .
                'If these elements are not present in the route template, then the default values for them must be defined in the route.'
            );
        }

        $this->segmentsNamed = $namedSegments;

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
            if ($segments[$i]->optional && $i - 1 >= 0) {
                $optional = true;

                for ($j = $i + 1; $j < $count; ++$j) {
                    if (!$segments[$j]->optional) {
                        $optional = false;
                        break;
                    }
                }

                if ($optional) {
                    $segments[$i - 1]->preEnd = true;
                }
                else {
                    for (--$j, $jc = $i - 1; $j >= $jc; --$j) {
                        $segments[$j]->optional = false;
                    }
                }
            }

            if ($i + 1 == $count) {
                $segments[$i]->end = true;
            }
        }
    }

    /**
     * Returns $template of the current instance.
     * 
     * @return string
     */
    public function __toString() {
        return $this->template;
    }

}