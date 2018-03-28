<?php
namespace PhpMvc;

/**
 * Occurs when the view file could not be found.
 */
class ViewNotFoundException extends \Exception {

    public function __construct($path) {
        $message = "The view file could not be found. Search paths:\r\n";
        $message .= $path . "\r\n";
        $message .= PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . $path . "\r\n";
        $message .= PHPMVC_SHARED_PATH . $path . "\r\n";

        if (strlen($path) > 4 && substr($path, -4) != '.php') {
            $path .= '.php';
            $message .= $path . "\r\n";
            $message .= PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . $path . "\r\n";
            $message .= PHPMVC_SHARED_PATH . $path . "\r\n";
        }

        parent::__construct($message);
    }

}