<?php
namespace PhpMvc;

/**
  * Represents base class for the HTTP request.
 */
abstract class HttpRequestBase {

    /**
     * Gets or sets information about the URL of the current request.
     * 
     * @var array
     */
    protected $url = null;

    /**
     * URI of the current request.
     * 
     * @var string
     */
    protected $rawUrl = null;

    /**
     * Query string of the current request.
     * 
     * @var QueryString
     */
    protected $queryString = null;

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

        $this->rawUrl = $this->serverVariables['REQUEST_URI'];
    }

    /**
     * Returns URL components.
     * 
     * Potential keys within this array are:
     * - scheme - e.g. http
     * - host
     * - port
     * - user
     * - pass
     * - path
     * - query - after the question mark ?
     * - fragment - after the hashmark #
     * 
     * @return array
     */
    public function url() {
        // TODO: class for url
        if ($this->url === null) {
            $this->url = parse_url(
                'http' .
                (isset($serverVariables['HTTPS']) ? 's' : '') .
                '://' .
                $serverVariables['HTTP_HOST'] .
                $serverVariables['REQUEST_URI']
            );
        }

        return $this->url;
    }

    /**
     * Gets the raw URL of the current request.
     * For example: /home/example
     * 
     * @return string
     */
    public function rawUrl () {
        return $this->rawUrl;
    }

    /**
     * Gets information about the URL of the client's previous request that linked to the current URL.
     * 
     * @return string
     */
    public function urlReferrer() {
        return $this->serverVariables['HTTP_REFERER'];
    }

    /**
     * Returns query string.
     * 
     * @param string|null $key The key to get. Default: null - QueryString instance.
     * 
     * @return QueryString|string
     */
    public function queryString($key = null) {
        if ($this->queryString === null) {
            $this->queryString = new QueryString();
            $queryString = array();
    
            if (($qsIndex = strpos($this->rawUrl, '?')) !== false) {
                parse_str(substr($this->rawUrl, $qsIndex + 1), $queryString);
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

        return $this->getSingleKeyOrAll($this->queryString, $key);
    }

    /**
     * Gets the document root directory under which the current script is executing, as defined in the server's configuration file.
     * 
     * @return string
     */
    public function documentRoot() {
        return $this->serverVariables['DOCUMENT_ROOT'];
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
     * Gets a sorted string array of client language preferences.
     * 
     * @return array
     */
    public function userLanguages() {
        if (isset($this->serverVariables['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all(
                '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
                $this->serverVariables['HTTP_ACCEPT_LANGUAGE'],
                $matches
            );

            if (count($matches[1])) {
                $result = array_combine($matches[1], $matches[4]);

                array_walk($result, function($lang, $amount) {
                    if ($amount === '') {
                        $langs[$lang] = 1;
                    }
                });

                arsort($result, \SORT_NUMERIC);

                return $result;
            }
        }

        return array();
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