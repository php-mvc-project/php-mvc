<?php
namespace PhpMvc;

/**
 * Represents base class for the request context.
 */
abstract class RequestContextBase {

    /**
     * URI of the current request.
     * 
     * @var string
     */
    protected $requestUri;

    /**
     * Query string of the current request.
     * 
     * @var string
     */
    protected $queryString;

    /**
     * Server variables.
     * 
     * @var array
     */
    protected $serverVariables;

    protected function __construct($serverVariables) {
        $this->serverVariables = $serverVariables;
        $this->requestUri = $serverVariables['REQUEST_URI'];
        $this->queryString = !empty($serverVariables['QUERY_STRING']) ? $serverVariables['QUERY_STRING'] : null;
    }

    /**
     * Return request URI.
     * 
     * @return string
     */
    public function getRequestUri() {
        return $this->requestUri;
    }

    /**
     * Return query string.
     * 
     * @return string
     */
    public function getQueryString() {
        return $this->queryString;
    }

    /**
     * Return server variables.
     * 
     * @return array
     */
    public function getServerVariables() {
        return $this->queryString;
    }

}