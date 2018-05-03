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

    /**
     * Gets an object that contains the view context information for the parent action method.
     * 
     * @var ViewContext
     */
    public $parentActionViewContext;

    /**
     * Gets or sets parent view context.
     * 
     * @var ViewContext
     */
    public $parent;

    /**
     * Gets or sets body view context.
     * 
     * @var ViewContext
     */
    public $body;

    /**
     * Initializes a new instance of ViewContext.
     * 
     * @param string $viewFile The view file path.
     * @param ActionContext $actionContext The action context of the current request.
     * @param ActionResult $actionResult The result of the action.
     * @param mixed $model The view model.
     * @param array $viewData The view data.
     * @param ViewContext $parentActionViewContext The context of the main view.
     * @param ViewContext $parent The context of the parent.
     * @param ViewContext $body The context of the main child.
     */
    public function __construct($viewFile, $actionContext, $actionResult, $model, $viewData, $parentActionViewContext = null, $parent = null, $body = null) {
        parent::__construct($actionContext->getHttpContext(), $actionContext->getRoute());

        $this->controller = $actionContext->getController();
        $this->modelState = $actionContext->getModelState();
        $this->arguments = $actionContext->getArguments();
        $this->actionName = $actionContext->getActionName();

        $this->viewFile = $viewFile;
        $this->model = $model;
        $this->viewData = $viewData;
        $this->actionResult = $actionResult;
        $this->parentActionViewContext = $parentActionViewContext;
        $this->parent = $parent;
        $this->body = $body;
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