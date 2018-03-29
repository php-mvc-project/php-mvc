<?php
namespace PhpMvc;

/**
 * Represents the properties and methods that are needed to render a view.
 */
class ViewResult implements ActionResult {

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
     * Gets or sets model.
     * 
     * @var mixed
     */
    public $model;

    /**
     * Gets or sets view data.
     */
    public $viewData = array();

    public function __construct($model = null, $layout = null) {
        $this->model = $model;
        $this->layout = $layout;
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
        !empty($this->layout) ? ViewContext::$layout = $this->layout : null;
        !empty($this->title) ? ViewContext::$title = $this->title : null;
        
        if (!empty($this->viewData)) {
            ViewContext::$viewData = array_unique(array_merge(ViewContext::$viewData, $this->viewData), SORT_REGULAR);
        }

        ViewContext::$actionResult = $this->model;
    }

}