<?php
namespace PhpMvc;

/**
 * Represents the properties and methods that are needed to render a view.
 */
class View {

    /**
     * Sets layout.
     * 
     * @param string $path The layout file name in the shared folder or full path to layout file.
     * @return void
     */
    public static function setLayout($path) {
        ViewContext::$layout = $path;
    }

    /**
     * Sets page title.
     * 
     * @return void
     */
    public static function setTitle($title) {
        ViewContext::$title = $title;
    }

    public static function injectModel(&$model) {
        if (!empty(ViewContext::$actionResult)) {
            $model = ViewContext::$actionResult;
        }
    }

    public static function setData($key, $value) {
        ViewContext::$viewData[$key] = $value;
    }

    public static function getData($key) {
        return ViewContext::$viewData[$key];
    }

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