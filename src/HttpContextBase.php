<?php
namespace PhpMvc;

/**
 * Serves as the base class for classes that contain HTTP-specific information about an individual HTTP request.
 */
abstract class HttpContextBase {

    /**
     * When overridden in a derived class, gets the HttpRequest object for the current HTTP request.
     * 
     * @var HttpRequestBase
     */
    protected $request;

    /**
     * When overridden in a derived class, gets the HttpResponse object for the current HTTP request.
     * 
     * @var HttpResponseBase
     */
    protected $response;

    /**
     * Initializes a new instance of the HttpContextBase.
     */
    public function __construct($request, $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Gets request.
     * 
     * @return HttpRequestBase
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Gets response.
     * 
     * @return HttpResponseBase
     */
    public function getResponse() {
        return $this->response;
    }

}