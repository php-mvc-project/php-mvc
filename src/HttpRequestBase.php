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
     * @var QueryString
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
     * HTTP headers of the current request.
     * 
     * @var array
     */
    private $headers = null;

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

        $this->queryString = new QueryString();
        $queryString = array();

        if (($qsIndex = strpos($this->requestUri, '?')) !== false) {
            parse_str(substr($this->requestUri, $qsIndex + 1), $queryString);
        }
        else {
            if (!empty($serverVariables['QUERY_STRING'])) {
                parse_str($serverVariables['QUERY_STRING'], $queryString);
            }
        }

        if (!empty($queryString)) {
            foreach ($queryString as $key => $value) {
                $this->queryString[$key] = $value;
            }
        }
    }

    /**
     * Returns request URI.
     * 
     * @return string
     */
    public function requestUri() {
        return $this->requestUri;
    }

    /**
     * Returns query string.
     * 
     * @param string|null $key The key to get. Default: null - QueryString instance.
     * 
     * @return QueryString|string
     */
    public function queryString($key = null) {
        return $this->getSingleKeyOrAll($this->queryString, $key);
    }

    /**
     * Returns server variables.
     * 
     * @param string|null $key The key to get. Default: null - all variables.
     * 
     * @return array|string
     */
    public function serverVariables($key = null) {
        return $this->getSingleKeyOrAll($this->serverVariables, $key);
    }

    /**
     * Returns cookies.
     * 
     * @param string|null $key The cookie name to get. Default: null - all cookies.
     * 
     * @return array|string
     */
    public function cookies($key = null) {
        return $this->getSingleKeyOrAll($this->cookies, $key);
    }

    /**
     * Returns session.
     * 
     * @param string|null $key The session key to get. Default: null - all keys.
     * 
     * @return array|mixed
     */
    public function session($key = null) {
        return $this->getSingleKeyOrAll($this->session, $key);
    }

    /**
     * Returns GET data.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * 
     * @return array|mixed
     */
    public function get($key = null) {
        return $this->getSingleKeyOrAll($this->get, $key);
    }

    /**
     * Returns POST data.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * 
     * @return array|mixed
     */
    public function post($key = null) {
        return $this->getSingleKeyOrAll($this->post, $key);
    }

    /**
     * Returns posted files.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * 
     * @return array|mixed
     */
    public function files($key = null) {
        return $this->getSingleKeyOrAll($this->files, $key);
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
     * @param string|null $key The key to get. Default: null - all keys.
     * 
     * @return array|string
     */
    public function headers($key = null) {
        if ($this->headers === null) {
            $result = array(); 

            foreach ($this->serverVariables as $k => $v)
            {
                if (substr($k, 0, 5) == 'HTTP_') 
                {
                    $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($k, 5)))));
                    $result[$headerName] = $v;
                }
            }

            $this->headers = $result;
        }

        return $this->getSingleKeyOrAll($this->headers, $key);
    }

    /**
     * Gets single key from array or all keys, if key is null.
     */
    private function getSingleKeyOrAll($array, $key) {
        if ($key !== null) {
            return (isset($array[$key])) ? $array[$key] : null;
        }
        else {
            return $array;
        }
    }

}