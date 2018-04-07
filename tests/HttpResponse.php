<?php
use PhpMvc\HttpResponseBase;

final class HttpResponse extends HttpResponseBase {

    /**
     * Gets or sets output data.
     */
    private $output = '';

    /**
     * Files to output.
     */
    private $files = array();

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
        $this->files = array();
        $this->output = '';
    }

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