<?php
namespace PhpMvc;

/**
 * Represents the information needed to create an HTTP request context.
 */
class HttpContextInfo {

    /**
     * Gets or sets route provider.
     * 
     * @var RouteProvider
     */
    public $routeProvider;

    /**
     * Gets or sets cache provider.
     * 
     * @var CacheProvider
     */
    public $cacheProvider;

    /**
     * Gets or sets request.
     * 
     * @var HttpRequestBase
     */
    public $request;

    /**
     * Gets or sets response.
     * 
     * @var HttpResponseBase
     */
    public $response;

    /**
     * Gets or sets session variables.
     * 
     * @var array
     */
    public $session;

}