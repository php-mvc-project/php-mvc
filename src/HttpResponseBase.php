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
 * Represents the base class for classes that provides HTTP-response information.
 */
abstract class HttpResponseBase {

    /**
     * The "flush" event handler.
     * 
     * @var callback
     */
    private $flushHandler;

    /**
     * The "preSend" event handler.
     * 
     * @var callback
     */
    private $preSendHandler;

    /**
     * The "end" event handler.
     * 
     * @var callback
     */
    private $endHandler;

    /**
     * Sets or sets the HTTP status code of the output that is returned to the client.
     * 
     * @var int
     */
    protected $statusCode;

    /**
     * Gets or sets the HTTP status message of the output that is returned to the client.
     * 
     * @var string
     */
    protected $statusDescription;

    /**
     * Gets or sets the collection of response headers.
     * 
     * @var array
     */
    protected $headers = array();

    /**
     * Gets or sets cookies list.
     * 
     * @var array
     */
    protected $cookies = array();

    /**
     * Data to output.
     * 
     * @var string
     */
    protected $output = '';

    /**
     * Indicates whether the output is started or not.
     * 
     * @var bool
     */
    private $outputStarted = false;

    /**
     * Files to output.
     * 
     * @var array
     */
    protected $files = array();

    /**
     * Returns the value indicating whether the output was started or not.
     * 
     * @return bool
     */
    protected function outputStarted() {
        return $this->outputStarted;
    }

    /**
     * Sets the event handler when calling the "flush" method.
     * 
     * @param callback|null $value The function to set.
     * 
     * @return void
     */
    public function setFlushHandler($value) {
        if ($value !== null && !is_callable($value)) {
            throw new \Exception('A function is expected, or null.');
        }

        $this->flushHandler = $value;
    }

    /**
     * Sets the event handler when calling the "preSend" method.
     * 
     * @param callback|null $value The function to set.
     * 
     * @return void
     */
    public function setPreSendHandler($value) {
        if ($value !== null && !is_callable($value)) {
            throw new \Exception('A function is expected, or null.');
        }

        $this->preSendHandler = $value;
    }

    /**
     * Sets the event handler when calling the "end" method.
     * 
     * @param callback|null $value The function to set.
     * 
     * @return void
     */
    public function setEndHandler($value) {
        if ($value !== null && !is_callable($value)) {
            throw new \Exception('A function is expected, or null.');
        }

        $this->endHandler = $value;
    }

    /**
     * Sets the HTTP status code of the output that is returned to the client.
     * 
     * @param int $statusCode HTTP status code to set.
     * 
     * @return void
     */
    public function setStatusCode($statusCode) {
        if ($this->canSetHeaders()) {
            $this->statusCode = intval($statusCode);
        }
    }

    /**
     * Gets the HTTP status code.
     * 
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Sets the HTTP status message of the output that is returned to the client.
     * 
     * @param string $statusDescription HTTP status message to set.
     * 
     * @return void
     */
    public function setStatusDescription($statusDescription) {
        if ($this->canSetHeaders()) {
            $this->statusDescription = $statusDescription;
        }
    }

    /**
     * Gets the HTTP status message.
     * 
     * @return string
     */
    public function getStatusDescription() {
        return $this->statusDescription;
    }

    /**
     * Adds an HTTP header to the current response. 
     * 
     * @param string @name The name of the HTTP header to add value to.
     * @param string @value The string to add to the header.
     * 
     * @return void
     */
    public function addHeader($name, $value = null) {
        if ($this->canSetHeaders()) {
            if ($value === null) {
                $value = trim(substr($name, strpos($name, ':') + 1));
                $name = trim(substr($name, 0, strpos($name, ':')));
            }

            $this->headers[$name] = $value;
        }
    }

    /**
     * Sets HTTP headers. 
     * 
     * @param array $headers The HTTP headers to set.
     * 
     * @return void
     */
    public function setHeaders($headers) {
        $this->headers = is_array($headers) ? $headers : array();
    }

    /**
     * Gets HTTP headers. 
     * 
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Adds cookie to send to the client.
     * 
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expire The time the cookie expires.
     * This is a Unix timestamp so is in number of seconds since the epoch.
     * In other words, you'll most likely set this with the time() function plus the number of seconds before you want it to expire.
     * Or you might use mktime(). time()+60*60*24*30 will set the cookie to expire in 30 days.
     * If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
     * @param string $path The path on the server in which the cookie will be available on.
     * If set to '/', the cookie will be available within the entire $domain.
     * If set to '/foo/', the cookie will only be available within the /foo/ directory and all sub-directories such as /foo/bar/ of $domain.
     * The default value is the current directory that the cookie is being set in.
     * @param string $domain The (sub)domain that the cookie is available to.
     * Setting this to a subdomain (such as 'www.example.com') will make the cookie available to that subdomain and all other sub-domains of it (i.e. w2.www.example.com).
     * To make the cookie available to the whole domain (including all subdomains of it), simply set the value to the domain name ('example.com', in this case).
     * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
     * When set to TRUE, the cookie will only be set if a secure connection exists.
     * On the server-side, it's on the programmer to send this kind of cookie only on secure connection.
     * @param bool $httponly When TRUE the cookie will be made accessible only through the HTTP protocol.
     * This means that the cookie won't be accessible by scripting languages, such as JavaScript.
     * It has been suggested that this setting can effectively help to reduce identity theft through XSS attacks (although it is not supported by all browsers),
     * but that claim is often disputed. 
     * 
     * @return void
     */
    public function addCookie($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false) {
        if ($this->canSetHeaders()) {
            $this->cookies[] = func_get_args();
        }
    }

    /**
     * Sets cookies.
     * 
     * @param array $cookies The cookies to set.
     * 
     * @return void
     */
    public function setCookies($cookies) {
        $this->cookies = is_array($cookies) ? $cookies : array();
    }

    /**
     * Gets cookies.
     * 
     * @return array
     */
    public function getCookies() {
        return $this->cookies;
    }

    /**
     * Writes the specified string to the HTTP response output stream.
     * 
     * @param string|array $value The string to write to the HTTP output stream.
     * @param string $glue The string for gluing together the elements of an array, if $value is an array. Default: "\n".
     * 
     * @return void
     */
    public function write($value, $glue = "\n") {
        if (is_array($value)) {
            $this->output .= implode(chr(10), $value);
        }
        else {
            $this->output .= $value;
        }
    }

    /**
     * Writes the contents of the specified file to the HTTP response output stream.
     * 
     * @param string|array $path The path of the file to write to the HTTP output.
     * 
     * @return void
     */
    public function writeFile($path) {
        if (is_array($path)) {
            $this->files = array_merge($this->files, $path);
        }
        elseif (!empty($path)) {
            $this->files[] = $path;
        }
    }

    /**
     * Clears all headers and content output from the current response.
     * 
     * @return void
     */
    public function clear() {
        $this->header = array();
        $this->cookies = array();
        $this->files = array();
        $this->statusCode = null;
        $this->statusDescription = null;
        $this->output = '';

        if (ob_get_length() > 0) { 
            ob_clean(); 
        }
    }

    /**
     * Sends all currently buffered output to the client.
     * 
     * @return void
     */
    public function flush() {
        $this->outputStarted = true;

        $files = $this->files;
        $output = $this->output;

        $this->files = array();
        $this->output = '';

        if ($this->flushHandler !== null) {
            call_user_func($this->flushHandler, array(
                    'files' => $files,
                    'output' => $output
                )
            );
        }
    }

    /**
     * Calls the handler preSendHandler.
     * 
     * @return void
     */
    public function preSend() {
        if ($this->preSendHandler !== null) {
            call_user_func($this->preSendHandler);
        }
    }

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    public function end() {
        if ($this->endHandler !== null) {
            call_user_func($this->endHandler);
        }
    }

    private function canSetHeaders() {
        if ($this->outputStarted) {
            trigger_error(
                'Cannot modify header information - headers already sent.', 
                \E_USER_WARNING
            );

            return false;
        }
        else {
            return true;
        }
    }

}