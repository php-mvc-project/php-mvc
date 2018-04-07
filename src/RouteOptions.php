<?php
namespace PhpMvc;

/**
 * Represents route settings.
 */
class RouteOptions {

    /**
     * Gets or sets a value indicating whether all generated URLs are lower-case.
     * 
     * @var bool
     */
    public $lowercaseUrls;

    /**
     * Gets or sets a value indicating whether a trailing slash should be appended to the generated URLs.
     */
    public $appendTrailingSlash;

}