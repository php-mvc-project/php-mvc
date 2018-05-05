<?php
use PhpMvc\HttpContextInfo as HttpContextInfoBase;

/**
 * Represents the information needed to create an HTTP request context.
 */
class HttpContextInfo extends HttpContextInfoBase {

    /**
     * @var bool
     */
    public $noOutputHeaders = false;

    /**
     * @var array
     */
    public $serverVariables;

    /**
     * @var array
     */
    public $get;

    /**
     * @var array
     */
    public $post;

}