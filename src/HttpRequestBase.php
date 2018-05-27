<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
     * Path of the current request.
     * 
     * @var string
     */
    protected $path = null;

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
    protected $headers = null;

    /**
     * Document root path.
     * 
     * @var string
     */
    protected $documentRoot = null;

    /**
     * Initializes a new instance of the HttpRequestBase with the specified parameters.
     */
    protected function __construct(
        $serverVariables, 
        $cookies = array(), 
        $get = array(), 
        $post = array(), 
        $files = array()
    ) {
        $this->serverVariables = $serverVariables;
        
        $this->cookies = $cookies;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
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
     * For example, for the URL https://example.org/home/example the rawUrl is /home/example.
     * For the URL https://example.org/home/example?search=123 the rawUrl is /home/example?search=123.
     * 
     * @return string
     */
    public function rawUrl() {
        if ($this->rawUrl === null) {
            $this->rawUrl = (isset($this->serverVariables['REQUEST_URI']) ? $this->serverVariables['REQUEST_URI'] : '');
        }

        return $this->rawUrl;
    }

    /**
     * Gets the virtual path of the current request.
     * For example, for the URL https://example.org/home/example the path is /home/example.
     * For the URL https://example.org/home/example?search=123 the path is /home/example (without query string).
     * 
     * @return string
     */
    public function path() {
        if ($this->path === null) {
            $rawUrl = $this->rawUrl();

            if (($qsIndex = strpos($rawUrl, '?')) !== false) {
                $this->path = substr($rawUrl, 0, $qsIndex);
            }
            else {
                $this->path = $rawUrl;
            }
        }

        return $this->path;
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
     * @return string
     */
    public function queryString() {
        return $this->serverVariables['QUERY_STRING'];
    }

    /**
     * Gets the document root directory under which the current script is executing, as defined in the server's configuration file.
     * 
     * @return string
     */
    public function documentRoot() {
        if ($this->documentRoot == null) {
            $this->documentRoot = isset($this->serverVariables['DOCUMENT_ROOT']) ? $this->serverVariables['DOCUMENT_ROOT'] : substr(PHPMVC_ROOT_PATH, 0, -1);
        }

        return $this->documentRoot;
    }

    /**
     * Returns server variables.
     * 
     * @param string|null $key The key to get. Default: null - all variables.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|string
     */
    public function server($key = null, $default = null, $nullIfEmpty = false) {
        return InternalHelper::getSingleKeyOrAll($this->serverVariables, $key, $default, $nullIfEmpty);
    }

    /**
     * Returns cookies.
     * 
     * @param string|null $key The cookie name to get. Default: null - all cookies.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|string
     */
    public function cookies($key = null, $default = null, $nullIfEmpty = false) {
        return InternalHelper::getSingleKeyOrAll($this->cookies, $key, $default, $nullIfEmpty);
    }

    /**
     * Returns GET data.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|mixed
     */
    public function get($key = null, $default = null, $nullIfEmpty = false) {
        return InternalHelper::getSingleKeyOrAll($this->get, $key, $default, $nullIfEmpty);
    }

    /**
     * Returns POST data.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|mixed
     */
    public function post($key = null, $default = null, $nullIfEmpty = false) {
        return InternalHelper::getSingleKeyOrAll($this->post, $key, $default, $nullIfEmpty);
    }

    /**
     * Returns posted files.
     * 
     * @param string|null $key The key to get. Default: null - all keys.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|mixed
     */
    public function files($key = null, $default = null, $nullIfEmpty = false) {
        return InternalHelper::getSingleKeyOrAll($this->files, $key, $default, $nullIfEmpty);
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
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|string
     */
    public function headers($key = null, $default = null, $nullIfEmpty = false) {
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

        return InternalHelper::getSingleKeyOrAll($this->headers, $key, $default, $nullIfEmpty);
    }

}