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
     * @param string|bool $downloadName the content-disposition header so that a file-download dialog box is displayed in the browser with the specified file name.
     */
    public function __construct($path, $contentType = 'application/octet-stream', $downloadName = null) {
        $this->path = $path;
        $this->contentType = $contentType;
        $this->downloadName = $downloadName;

        if ($downloadName === true) {
            $downloadName = basename($this->path);
        }
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
        if (($path = PathUtility::getFilePath($this->path)) === false) {
            throw new \Exception('File "' . $this->path . '" not found.');
        }

        $response = $actionContext->getHttpContext()->getResponse();
        $response->addHeader('Content-Type', (!empty($this->contentType) ? $this->contentType : 'application/octet-stream'));
        $response->addHeader('Content-Length', filesize($path));

        if (!empty($this->downloadName)) {
            $response->addHeader('Content-Disposition', 'attachment; filename="' . $this->downloadName . '"');
        }

        $response->writeFile($path);

        $response->end();
    }

}