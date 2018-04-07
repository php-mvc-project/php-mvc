<?php
namespace PhpMvc;

/**
 * Represents segment of route.
 */
class RouteSegment {

    /**
     * Segment name.
     * 
     * @var string
     */
    public $name;

    /**
     * Regular expressions pattern.
     * 
     * @var string
     */
    public $pattern;

    /**
     * Indicates that the parameter is optional..
     * 
     * @var bool
     */
    public $optional;

    /**
     * Default value.
     * 
     * @return string
     */
    public $default;

    /**
     * Before text.
     * 
     * @var string
     */
    public $before;

    /**
     * After text.
     * 
     * @var string
     */
    public $after;

    /**
     * Glue to the previous segment.
     * 
     * @var bool
     */
    public $glued;

    /**
     * Is pre-end.
     * 
     * @var bool
     */
    public $preEnd;

    /**
     * The end :)
     * 
     * @var bool
     */
    public $end;

}