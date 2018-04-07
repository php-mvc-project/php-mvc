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
     * Gets or sets view file.
     */
    public $viewFile = null;

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

    public function __construct($viewOrModel = null, $model = null, $layout = null) {
        if (gettype($viewOrModel) === 'string') {
            if (($path = PathUtility::getViewFilePath($viewOrModel)) !== false) {
                $this->viewFile = $path;
            }
            else {
                if (empty($model)) {
                    $model = $viewOrModel;
                }
                else {
                    throw new ViewNotFoundException($viewOrModel);
                }
            }
        }
        else {
            if (empty($model)) {
                $model = $viewOrModel;
            }
            else {
                throw new \Exception('The $viewOrModel parameter must contain the name of the view or the model. If the $viewOrModel parameter contains a model, the $model parameter value must be null.');
            }
        }

        $this->model = $model;
        $this->layout = $layout;
    }

    /**
     * Executes the action and outputs the result.
     * 
     * @param ViewContext $viewContext The view context.
     * 
     * @return void
     */
    public function execute($viewContext) {
        !empty($this->title) ? $viewContext->title = $this->title : null;
        !empty($this->viewFile) ? $viewContext->viewFile = PathUtility::getViewFilePath($this->viewFile) : null;
        !empty($this->layout) ? $viewContext->layout = PathUtility::getViewFilePath($this->layout) : null;
        !empty($this->model) ? $viewContext->model = $this->model : null;

        if (!empty($this->viewData)) {
            $viewContext->viewData = array_unique(array_merge($viewContext->viewData, $this->viewData), SORT_REGULAR);
        }
    }

}