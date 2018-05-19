<?php
namespace PhpMvc;

/**
 * Represents methods for managing the AntiForgeryToken validation.
 */
final class ValidateAntiForgeryToken {

    /**
     * Defines ActionContext.
     * 
     * @var ActionContext
     */
    private static $actionContext;

    /**
     * Gets or sets mode.
     * 
     * @var bool|null
     */
    private static $enable = null;

    /**
     * Disables verification for the specified action.
     * 
     * @param string $actionName Action name.
     * 
     * @return void
     */
    public static function disable($actionName = null) {
        self::set($actionName, false);
    }

    /**
     * Enables verification for the specified action.
     * 
     * @param string $actionName Action name.
     * 
     * @return void
     */
    public static function enable($actionName = null) {
        self::set($actionName, true);
    }

    /**
     * Sets verification mode for the specified action.
     * 
     * @param string $actionName Action name.
     * @param bool $enable Enable (true) or disable (false) verification.
     * 
     * @return void
     */
    public static function set($actionName = null, $enable) {
        $actionName = ($actionName === null ? '.' : $actionName);

        if ($actionName != '.' && !self::$actionContext->actionNameEquals($actionName)) {
            return;
        }

        self::$enable = $enable;
    }

}