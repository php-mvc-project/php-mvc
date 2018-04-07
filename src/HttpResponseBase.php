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
     * Writes the specified string to the HTTP response output stream.
     * 
     * @param string $value The string to write to the HTTP output stream.
     * 
     * @return void
     */
    abstract public function write($value);

    /**
     * Writes the contents of the specified file to the HTTP response output stream.
     * 
     * @param string $path The path of the file to write to the HTTP output.
     * 
     * @return void
     */
    abstract public function writeFile($path);

    /**
     * Clears all headers and content output from the current response.
     * 
     * @return void
     */
    abstract public function clear();

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    abstract public function end();

}