<?php
use PhpMvc\HttpContextBase;

require_once 'HttpRequest.php';
require_once 'HttpResponse.php';

final class HttpContext extends HttpContextBase {

    public function __construct($serverVariables, $get = array(), $post = array()) {
        parent::__construct(
            new HttpRequest($serverVariables, $get, $post), 
            new HttpResponse()
        );
    }
    
}