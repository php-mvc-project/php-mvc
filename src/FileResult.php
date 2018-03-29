<?php
namespace PhpMvc;

/**
 * Represents class that is used to send binary file content to the response.
 */
class FileResult implements ActionResult {

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
    
    /**
     * Executes the action and outputs the result.
     * 
     * @param ActionContext $actionContext The context in which the result is executed.
     * The context information includes information about the action that was executed and request information.
     * 
     * @return void
     */
    public function execute($actionContext) {
        $fp = fopen($this->path, 'rb');
        
        // headers
        header('Content-Type: ' . (!empty($this->contentType) ? $this->contentType : 'application/octet-stream'));
        header('Content-Length: ' . filesize($this->path));
        
        if (!empty($this->downloadName)) {
            header('Content-Disposition: attachment; filename="' . $this->downloadName . '"');
        }

        // output and exit
        fpassthru($fp);

        exit;
    }

}