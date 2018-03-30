<?php
namespace PhpMvc;

/**
 * Controls the processing of application actions by redirecting to a specified URI.
 */
class RedirectResult implements ActionResult {

    /**
     * Gets or sets the target URL.
     * 
     * @var string
     */
    protected $url;

    /**
     * Gets or sets a value that indicates whether the redirection should be permanent.
     * 
     * @var bool
     */
    private $permanent;

    /**
     * Gets or sets an indication that the redirect preserves the initial request method.
     * 
     * @var bool
     */
    private $preserveMethod;

    /**
     * Initializes a new instance of RedirectResult.
     * 
     * @param int $url The target URL.
     * @param bool $permanent A value that indicates whether the redirection should be permanent.
     * @param bool $preserveMethod If set to true, make the temporary redirect (307) or permanent redirect (308) preserve the intial request method.
     */
    public function __construct($url, $permanent = false, $preserveMethod = false) {
        $this->url = $url;
        $this->permanent = $permanent;
        $this->preserveMethod = $preserveMethod;
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
        if ($this->permanent === true && $this->preserveMethod !== true) {
            http_response_code(301);
        }
        else if ($this->permanent !== true && $this->preserveMethod === true) {
            http_response_code(307);
        }
        else if ($this->permanent === true && $this->preserveMethod === true) {
            http_response_code(308);
        }

        header('Location: ' . $this->url);

        exit;
    }

}