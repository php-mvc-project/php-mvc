<?php
namespace PhpMvc;

/**
 * Occurs if the action name contains illegal characters.
 */
final class ActionNameValidationException extends \Exception {

    /**
     * Initializes a new instance of the ActionNameValidationException.
     * 
     */
    public function __construct() {
        parent::__construct('Action names can not begin with a "_".');
    }

}