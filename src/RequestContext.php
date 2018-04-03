<?php
namespace PhpMvc;

/**
 * Represents the context of the current request.
 */
final class RequestContext extends RequestContextBase {

    /**
     * Initializes a new instance of the RequestContext for the current request.
     */
    public function __construct() {
        parent::__construct($_SERVER, $_COOKIE, $_SESSION, $_GET, $_POST, $_FILES);
    }
    
}