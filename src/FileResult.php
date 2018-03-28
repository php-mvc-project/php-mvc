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
   
    /**
     * Initializes a new instance of FileResult.
     * 
     * @param string $path The file path to output.
     * @param string $contentType The content type.
     * @param string $downloadName the content-disposition header so that a file-download dialog box is displayed in the browser with the specified file name.
     */
    public function __construct($path, $contentType = null, $downloadName = null) {
        $this->path = $path;
        $this->contentType = $contentType;
        $this->downloadName = $downloadName;
    }

}