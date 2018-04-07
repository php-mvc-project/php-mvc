<?php
namespace PhpMvc;

/**
 * Represents current request.
 */
final class HttpRequest extends HttpRequestBase {

    /**
     * Initializes a new instance of the HttpRequest for the current request.
     */
    public function __construct() {
        parent::__construct($_SERVER, $_COOKIE, $_SESSION, $_GET, $_POST, $_FILES);
    }
    
}