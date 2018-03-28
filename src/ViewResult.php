<?php
namespace PhpMvc;

/**
 * Represents the properties and methods that are needed to render a view.
 */
class ViewResult {

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

}