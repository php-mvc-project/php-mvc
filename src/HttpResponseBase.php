<?php
namespace PhpMvc;

/**
 * Represents the base class for classes that provides HTTP-response information.
 */
abstract class HttpResponseBase {

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
     */
    protected $output = '';

    /**
     * Files to output.
     */
    protected $files = array();

    /**
     * Sets the HTTP status code of the output that is returned to the client.
     * 
     * @param int $statusCode HTTP status code to set.
     * 
     * @return void
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
    }

    /**
     * Sets the HTTP status message of the output that is returned to the client.
     * 
     * @param string $statusDescription HTTP status message to set.
     * 
     * @return void
     */
    public function setStatusDescription($statusDescription) {
        $this->statusDescription = $statusDescription;
    }

    /**
     * Adds an HTTP header to the current response. 
     * 
     * @param string @name The name of the HTTP header to add value to.
     * @param string @value The string to add to the header.
     * 
     * @return void
     */
    public function addHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * Send a cookie.
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
    public function setCookie(string $name, string $value = '', int $expire = 0, string $path = '', string $domain = '', bool $secure = false, bool $httponly = false) {
        $this->cookies[] = func_get_args();
    }

    /**
     * Writes the specified string to the HTTP response output stream.
     * 
     * @param string $value The string to write to the HTTP output stream.
     * 
     * @return void
     */
    public function write($value) {
        $this->output .= $value;
    }

    /**
     * Writes the contents of the specified file to the HTTP response output stream.
     * 
     * @param string $path The path of the file to write to the HTTP output.
     * 
     * @return void
     */
    public function writeFile($path) {
        $this->files[] = $path;
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
        $this->output = '';
    }

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    abstract public function end();

}