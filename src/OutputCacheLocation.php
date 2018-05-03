<?php
namespace PhpMvc;

/**
 * Specifies the valid values for controlling the location of the output-cached HTTP response for a resource.
 */
final class OutputCacheLocation {

    /**
     * The output cache can be located on the browser client (where the request originated), on a proxy server (or any other server) participating in the request, or on the server where the request was processed.
     * 
    * @var int
     */
    const ANY = 0;

    /**
     * The output cache is located on the browser client where the request originated.
     * 
    * @var int
     */
    const CLIENT = 1;

    /**
     * The output cache can be stored in any HTTP 1.1 cache-capable devices other than the origin server.
     * 
    * @var int
     */
    const DOWNSTREAM = 2;

    /**
     * The output cache is disabled for the requested page.
     * 
     * @var int
     */
    const NONE = 3;

    /**
     * The output cache is located on the Web server where the request was processed.
     * 
     * @var int
     */
    const SERVER = 4;

    /**
     * The output cache can be stored only at the origin server or at the requesting client.
     * 
     * @var int
     */
    const SERVER_AND_CLIENT = 5;

}