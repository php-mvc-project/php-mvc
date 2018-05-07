<?php
use PhpMvc\HttpRequestBase;

final class HttpRequest extends HttpRequestBase {

    public function __construct($serverVariables, $get = array(), $post = array()) {
        parent::__construct($serverVariables, array(), $get, $post);
    }
    
}