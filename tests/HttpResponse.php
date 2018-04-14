<?php
use PhpMvc\HttpResponseBase;

final class HttpResponse extends HttpResponseBase {

    /**
     * Sends all currently buffered output to the client and stops execution of the requested process.
     * 
     * @return void
     */
    public function end() {
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

        $this->clear();
    }

}