<?php
namespace PhpMvc;

/**
 * Represents the context of the current view.
 */
final class ViewContext {
 
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
     * 
     * @var ModelState
     */
    public static $modelState;

    /**
     * 
     * @var ActionContext
     */
    public static $actionContext;

    public static $viewFile;
    
    /**
     * 
     * @var ModelDataAnnotation[]
     */
    public static $modelDataAnnotations = array();

    /**
     * @param string @key
     * 
     * @return ModelDataAnnotation
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