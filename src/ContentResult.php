<?php
namespace PhpMvc;

/**
 * Represents a user-defined content type that is the result of an action method.
 */
class ContentResult implements ActionResult {

    /**
     * Gets or sets the content.
     * 
     * @var mixed
     */
    private $content;

    /**
     * Gets or sets the type of the content. For example: text/plain, text/html.
     * 
     * @var string
     */
    private $contentType;
   
    /**
     * Initializes a new instance of ContentResult.
     * 
     * @param string $content The content.
     * @param string $contentType The content type. Default: text/plain.
     */
    public function __construct($content, $contentType = 'text/plain') {
        $this->content = $content;
        $this->contentType = $contentType;
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
        $response = $actionContext->getHttpContext()->getResponse();
        $response->addHeader('Content-Type', (!empty($this->contentType) ? $this->contentType : 'text/plain'));
        $response->write($this->content);
        $response->end();
    }

}