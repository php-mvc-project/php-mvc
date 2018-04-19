<?php
namespace PhpMvc;

/**
 * Represents static methods for adding filters.
 */
final class Filter {

    /**
     * List of filters.
     * 
     * @var array
     */
    private static $filters = array();

    /**
     * Defines ActionContext.
     * 
     * @var ActionContext
     */
    private static $actionContext;

    /**
     * Adds a filter.
     * 
     * @param string $actionOrFilterName The action name or filter name, if the filter should be used for the current controller.
     * @param string $filterName The name of the filter, if the name of the action is specified in the first parameter.
     * 
     * @return void
     */
    public static function add($actionOrFilterName, $filterName = null) {
        if (empty($filterName)) {
            $filterName = $actionOrFilterName;
            $actionOrFilterName = '.';
        }

        if ($actionOrFilterName != '.' && !self::$actionContext->actionNameEquals($actionOrFilterName)) {
            return;
        }

        if (!isset(self::$filters[$actionOrFilterName])) {
            self::$filters[$actionOrFilterName] = array();
        }

        self::$filters[$actionOrFilterName][] = $filterName;
    }

}