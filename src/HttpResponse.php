<?php
namespace PhpMvc;

/**
 * Encapsulates HTTP-response information.
 */
final class HttpResponse extends HttpResponseBase {

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    public function end() {
        // status code and message
        if (!empty($this->statusCode) || !empty($this->statusDescription)) {
            $statusCode = $this->statusCode;

            if (empty($statusCode)) {
                $statusCode = 200;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $statusCode . ' ' . $this->statusDescription);
            $GLOBALS['http_response_code'] = $statusCode;
        }

        // headers
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        // cookies
        foreach ($this->cookies as $cookie) {
            call_user_func_array('setcookie', $cookie);
        }

        // files
        foreach ($this->files as $file) {
            $fp = fopen($file, 'rb');
            fpassthru($fp);
            fclose($fp);
        }

        // text
        if (!empty($this->output)) {
            echo $this->output;
        }

        // end
        exit();
    }

}