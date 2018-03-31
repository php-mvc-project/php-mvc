<?php
namespace PhpMvc;

/**
 * Represents the context of the current view.
 */
final class ViewContext {

    /**
     * Gets or sets Route of the current request.
     * 
     * @var Route
     */
    public static $route;

    /**
     * Gets or sets layout file.
     * 
     * @var string
     */
    public static $layout;

    /**
     * Gets or sets page title.
     * 
     * @var string
     */
    public static $title;

    /**
     * Gets or sets content of the view.
     * 
     * @var string
     */
    public static $content;

    /**
     * Gets or sets model.
     * 
     * @var mixed
     */
    public static $model;

    /**
     * Gets or sets view data.
     */
    public static $viewData = array();

    /**
     * Gets or sets result of action.
     */
    public static $actionResult;

    /**
     * Gets or sets ModelState.
     * 
     * @var ModelState
     */
    public static $modelState;

    /**
     * Gets or sets ActionContext.
     * 
     * @var ActionContext
     */
    public static $actionContext;

    /**
     * Gets or sets view file name.
     * 
     * @var string
     */
    public static $viewFile;

    /**
     * Gets or sets metadata of the $model.
     * 
     * @var ModelDataAnnotation[]
     */
    public static $modelDataAnnotations = array();

    /**
     * Gets the metadata for the specified model key.
     * If there is no data, it returns null.
     * 
     * @param string @key The key to get metadata.
     * 
     * @return ModelDataAnnotation|null
     */
    public static function getModelDataAnnotation($key) {
        if (array_key_exists($key, ViewContext::$modelDataAnnotations)) {
            return ViewContext::$modelDataAnnotations[$key];
        }
        else {
            return null;
        }
    }

}