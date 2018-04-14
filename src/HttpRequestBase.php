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
    public function requestUri() {
        return $this->requestUri;
    }

    /**
     * Return query string.
     * 
     * @return string
     */
    public function queryString() {
        return $this->queryString;
    }

    /**
     * Return server variables.
     * 
     * @return array
     */
    public function serverVariables() {
        return $this->serverVariables;
    }

    /**
     * Return cookies.
     * 
     * @return array
     */
    public function cookies() {
        return $this->cookies;
    }

    /**
     * Return session.
     * 
     * @return array
     */
    public function session() {
        return $this->session;
    }

    /**
     * Return GET data.
     * 
     * @return array
     */
    public function get() {
        return $this->get;
    }

    /**
     * Return POST data.
     * 
     * @return array
     */
    public function post() {
        return $this->post;
    }

    /**
     * Return posted files.
     * 
     * @return array
     */
    public function files() {
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
     * Gets the HTTP data transfer method (such as GET, POST, or HEAD) used by the client.
     * 
     * @return string
     */
    public function httpMethod() {
        return $this->serverVariables['REQUEST_METHOD'];
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
     * Returns user agent.
     * 
     * @return string
     */
    public function userAgent() {
        return $this->serverVariables['HTTP_USER_AGENT'];
    }

    /**
     * The IP address from which the user is viewing the current page.
     * 
     * @return string
     */
    public function userHostAddress() {
        return $this->serverVariables['REMOTE_ADDR'];
    }

    /**
     * Returns Content-Type of the request or empty string.
     * 
     * @return string
     */
    public function contentType() {
        $headers = array('CONTENT_TYPE', 'HTTP_CONTENT_TYPE');

        foreach ($headers as $header) {
            if (!empty($this->serverVariables[$header])) {
                return $this->serverVariables[$header];
            }
        }

        return '';
    }

    /**
     * Returns HTTP headers of the request.
     * 
     * @return array
     */
    public function headers() {
        $result = array(); 

        foreach ($this->serverVariables as $key => $value) 
        {
            if (substr($key, 0, 5) == 'HTTP_') 
            {
                $result[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value; 
            }
        }

        return $result; 
    }

}