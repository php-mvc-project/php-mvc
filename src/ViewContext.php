<?php
namespace PhpMvc;

/**
 * Represents the context of the current view.
 */
class ViewContext {
 
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

}