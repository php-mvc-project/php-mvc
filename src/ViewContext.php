<?php
namespace PhpMvc;

/**
 * Represents the context of the current view.
 */
final class ViewContext extends ActionContext {

    /**
     * Gets or sets layout file.
     * 
     * @var string
     */
    public $layout;

    /**
     * Gets or sets page title.
     * 
     * @var string
     */
    public $title;

    /**
     * Gets or sets content of the view.
     * 
     * @var string
     */
    public $content;

    /**
     * Gets or sets model.
     * 
     * @var mixed
     */
    public $model;

    /**
     * Gets or sets view data.
     * 
     * @var array
     */
    public $viewData = array();

    /**
     * Gets or sets result of action.
     * 
     * @var mixed
     */
    public $actionResult;

    /**
     * Gets or sets view file name.
     * 
     * @var string
     */
    public $viewFile;

    public function __construct($actionContext, $actionResult, $viewData) {
        parent::__construct($actionContext->httpContext, $actionContext->route);

        $this->controller = $actionContext->controller;
        $this->modelState = $actionContext->modelState;
        $this->arguments = $actionContext->arguments;
        $this->actionName = $actionContext->actionName;

        $this->viewData = $viewData;
        $this->actionResult = $actionResult;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public function getModelState() {
        return $this->modelState;
    }

    /**
     * Gets the metadata for the specified model key.
     * If there is no data, it returns null.
     * 
     * @param string @key The key to get metadata.
     * 
     * @return ModelDataAnnotation|null
     */
    public function getModelDataAnnotation($key) {
        if (array_key_exists($key, $this->modelState->annotations)) {
            return $this->modelState->annotations[$key];
        }
        else {
            return null;
        }
    }

}