<?php
use PhpMvc\HttpResponseBase;

final class HttpResponse extends HttpResponseBase {

    private $noOutputHeaders = false;

    public function __construct($noOutputHeaders = false) {
        $this->noOutputHeaders = $noOutputHeaders;
    }

    public function setNoOutputHeaders($value) {
        $this->noOutputHeaders = $value;
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
    }

    /**
     * Sends all currently buffered output to the client.
     * 
     * @return void
     */
    public function flush() {
        $this->output();
        parent::flush();
    }

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    public function end() {
        parent::preSend();
        $this->output();
        parent::end();

        $this->clear();
    }

    private function output() {
        if (!$this->outputStarted() && empty($this->files) && !$this->noOutputHeaders) {
            // status code and message
            if (!empty($this->statusCode) || !empty($this->statusDescription)) {
                $statusCode = $this->statusCode;

                if (empty($statusCode)) {
                    $statusCode = 200;
                }

                echo 'HTTP/1.1 ' . $statusCode . ' ' . $this->statusDescription;
            }
            else {
                echo 'HTTP/1.1 200 OK';
            }

            echo chr(10);

            // headers
            foreach ($this->headers as $name => $value) {
                echo $name . ': ' . $value . chr(10);
            }

            // cookies
            foreach ($this->cookies as $cookie) {
                echo 'Set-Cookie: ' . implode('; ', $cookie) . chr(10);
            }

            echo chr(10);
        }

        // files
        foreach ($this->files as $file) {
            $fp = fopen($file, 'rb');
            echo bin2hex(fread($fp, 10));
            fclose($fp);
        }

        // text
        if (!empty($this->output)) {
            echo $this->output;
        }
    }

}