<?php
namespace PhpMvc;

/**
 * Represents the properties and methods that are needed to render a view.
 */
final class View {

    /**
     * Sets layout.
     * 
     * @param string $path The layout file name in the shared folder or full path to layout file.
     * 
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

    /**
     * Injects model to state.
     * 
     * @param mixed &$model Model to injection.
     * 
     * @return void
     */
    public static function injectModel(&$model) {
        if (!empty(ViewContext::$actionResult)) {
            if (ViewContext::$actionResult instanceof ViewResult && !empty(ViewContext::$actionResult->model)) {
                $model = ViewContext::$actionResult->model;
            }
        }
    }

    /**
     * Sets data to view.
     * 
     * @param string $key Key associated with the data entry.
     * @param string $value The value to set.
     * 
     * @return void
     */
    public static function setData($key, $value) {
        ViewContext::$viewData[$key] = $value;
    }

    /**
     * Gets the data with the specified key.
     * If the specified key does not exist, function returns null.
     * 
     * @param string $key The key to get the data.
     * 
     * @return mixed|null
     */
    public static function getData($key) {
        return isset(ViewContext::$viewData[$key]) ? ViewContext::$viewData[$key] : null;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public static function getModelState() {
        return ViewContext::$modelState;
    }

}