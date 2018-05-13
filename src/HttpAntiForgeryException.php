<?php
namespace PhpMvc;

/**
 * An exception occurs when a fake HTTP security token is detected.
 */
final class HttpAntiForgeryException extends \Exception {

    /**
     * Initializes a new instance of the HttpAntiForgeryException.
     * 
     */
    public function __construct() {
        parent::__construct('Failed to decrypt the anti-forgery token.');
    }

}