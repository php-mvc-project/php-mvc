<?php
namespace PhpMvc;

/**
  * Represents base class for the HTTP request.
 */
abstract class HttpRequestBase {

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

    /**
     * An associative array of variables passed to the current script via HTTP Cookies.
     * 
     * @var array
     */
    protected $cookies;

    /**
     * An associative array containing session variables available to the current script.
     * 
     * @var array
     */
    protected $session;

    /**
     * An associative array of variables passed to the current script via the URL parameters.
     * 
     * @var array
     */
    protected $get;

    /**
     * An associative array of variables passed to the current script via the HTTP POST method when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request.
     * 
     * @var array
     */
    protected $post;

    /**
     * An associative array of items uploaded to the current script via the HTTP POST method.
     * 
     * @var array
     */
    protected $files;

    /**
     * Initializes a new instance of the HttpRequestBase with the specified parameters.
     */
    protected function __construct(
        $serverVariables, 
        $cookies = array(), 
        $session = array(), 
        $get = array(), 
        $post = array(), 
        $files = array()
    ) {
        $this->serverVariables = $serverVariables;
        
        $this->cookies = $cookies;
        $this->session = $session;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;

        $this->requestUri = $serverVariables['REQUEST_URI'];

        if (($qsIndex = strpos($this->requestUri, '?')) !== false) {
            $this->queryString = substr($this->requestUri, $qsIndex + 1);
        }
        else {
            $this->queryString = !empty($serverVariables['QUERY_STRING']) ? $serverVariables['QUERY_STRING'] : null;
        }
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
        return $this->serverVariables;
    }

    /**
     * Return cookies.
     * 
     * @return array
     */
    public function getCookies() {
        return $this->cookies;
    }

    /**
     * Return session.
     * 
     * @return array
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Return GET data.
     * 
     * @return array
     */
    public function getGetData() {
        return $this->get;
    }

    /**
     * Return POST data.
     * 
     * @return array
     */
    public function getPostData() {
        return $this->post;
    }

    /**
     * Return posted files.
     * 
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * Returns TRUE if the request is POST.
     * 
     * @return bool
     */
    public function isPost() {
        return $this->serverVariables['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Gets a value indicating whether the HTTP connection uses secure sockets (HTTPS).
     * 
     * @return bool
     */
    public function isSecureConnection() {
        return (!empty($this->serverVariables['HTTPS']) && $this->serverVariables['HTTPS'] !== 'off') || $this->serverVariables['SERVER_PORT'] == 443;
    }

    /**
     * Returns HTTP method of the request.
     * 
     * @return string
     */
    public function httpMethod() {
        return $this->serverVariables['REQUEST_METHOD'];
    }

    /**
     * Returns Content-Type of the request or empty string.
     * 
     * @return string
     */
    public function getContentType() {
        $headers = array('CONTENT_TYPE', 'HTTP_CONTENT_TYPE');

        foreach ($headers as $header) {
            if (!empty($this->serverVariables[$header])) {
                return $this->serverVariables[$header];
            }
        }
        
        return '';
    }

}