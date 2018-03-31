<?php
namespace PhpMvc;

/**
 * Represents the context of the current request.
 */
final class RequestContext extends RequestContextBase {

    public function __construct() {
        parent::__construct($_SERVER);
    }
    
}