<?php
use PhpMvc\RequestContextBase;

final class RequestContext extends RequestContextBase {

    public function __construct($serverVariables) {
        parent::__construct($serverVariables);
    }
    
}