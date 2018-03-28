<?php
namespace PhpMvc;

/**
 * Represents class that is used to send binary file content to the response.
 */
class FileResult {

    /**
     * Gets or sets path to file for output to client.
     * 
     * @var string
     */
    public $path;

    /**
     * Gets the content type to use for the response.
     * 
     * @var string
     */
    public $contentType;

    /**
     * Gets or sets the content-disposition header so that a file-download dialog box is displayed in the browser with the specified file name.
     * 
     * @var string
     */
    public $downloadName;
   
    public function __construct($instance = null) {
        if (isset($instance) && $instance instanceof FileResult) {
            $this->path = $instance->path;
            $this->contentType = $instance->contentType;
            $this->downloadName = $instance->downloadName;
        }
    }

}