<?php
namespace PhpMvc;

/**
 * Represents route settings.
 */
class RouteOptions {

    /**
     * Gets or sets a value indicating whether all generated URLs are lower-case.
     * Default: true.
     * 
     * @var bool
     */
    public $lowercaseUrls = true;

    /**
     * Gets or sets a value indicating whether a trailing slash should be appended to the generated URLs.
     * Default: false.
     * 
     * @var bool
     */
    public $appendTrailingSlash = false;

}