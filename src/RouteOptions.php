<?php
namespace PhpMvc;

/**
 * Represents route settings.
 */
class RouteOptions {

    /**
     * Gets or sets a value indicating whether all generated URLs are lower-case.
     * Default: TRUE.
     * 
     * @var bool
     */
    public $lowercaseUrls = true;

    /**
     * Gets or sets a value indicating whether a trailing slash should be appended to the generated URLs.
     * Default: FALSE.
     * 
     * @var bool
     */
    public $appendTrailingSlash = false;

    /**
     * Indicates that the last segment should be deleted if the segment value is the default value.
     * Default: TRUE.
     * 
     * @var bool
     */
    public $removeLastSegmentIfValueIsDefault = true;

}