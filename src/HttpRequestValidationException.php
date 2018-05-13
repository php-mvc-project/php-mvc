<?php
namespace PhpMvc;

/**
 * The exception that is thrown when a potentially malicious input string is received from the client as part of the request data.
 */
final class HttpRequestValidationException extends \Exception {

    /**
     * Initializes a new instance of the HttpRequestValidationException.
     * 
     */
    public function __construct($source = null, $key = null, $value = null) {
        $specific = '';

        if (!empty($source) && !empty($key) && !empty($value)) {
            $specific = 'A potentially dangerous ' . $source . ' value was obtained from the client (' . $key . ' = "' . $value . '").' . chr(10) . chr(10);
        }

        parent::__construct(
            $specific . 
            'Request validation detected a potentially dangerous input value from the client and aborted the request. ' .
            'This might be an attemp of using cross-site scripting to compromise the security of your site. ' .
            'You can disable request validation using the code AppBuilder::useValidation(array(\'crossSiteScripting\' => false)).'
        );
    }

}