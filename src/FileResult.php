<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
    public function __construct($path, $contentType = null, $downloadName = null) {
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

        if (empty($this->contentType) && function_exists('finfo_open') && ($fi = finfo_file(finfo_open(\FILEINFO_MIME_TYPE), $path) !== false)) {
            $this->contentType = $fi;
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