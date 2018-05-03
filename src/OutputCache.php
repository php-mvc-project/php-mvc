<?php
namespace PhpMvc;

/**
 * Represents methods for caching output results.
 */
final class OutputCache {

    /**
     * Defines ActionContext.
     * 
     * @var ActionContext
     */
    private static $actionContext;

    /**
     * Output cache settings.
     * 
     * @var array
     */
    private static $settings = array();

    /**
     * Sets the cache settings.
     * 
     * @param string $actionName Action name.
     * @param int $duration A cache duration value, in seconds.
     * @param int $location A cache location. Please see OutputCacheLocation class.
     * @param string|null $varyByParam A semicolon-separated list of strings that correspond to query-string values for the GET method, or to parameter values for the POST method.
     * @param string|null $varyByHeader A semicolon-separated list of HTTP headers that are used to vary the output cache.
     * @param callback|null $varyByCustom A function to check.
     * For example: function($actionContext) {
     *   $userAgent = $actionContext->getHttpContext()->getRequest()->server('HTTP_USER_AGENT');
     *   $browser = get_browser($userAgent, true);
     * 
     *   if ($browser['browser'] == 'Firefox') {
     *     return true;
     *   }
     *   else {
     *     return false;
     *   }
     * }
     * 
     * @return void
     */
    public static function set($actionName, $duration, $location, $varyByParam = null, $varyByHeader = null, $varyByCustom = null) {
        if (!self::canCache($actionName)) { return; }

        self::$settings[$actionName]['duration'] = $duration;
        self::$settings[$actionName]['location'] = $location;

        if (!empty($varyByParam)) {
            self::$settings[$actionName]['varyByParam'] = $varyByParam;
        }

        if (!empty($varyByHeader)) {
            self::$settings[$actionName]['varyByHeader'] = $varyByHeader;
        }

        if (!empty($varyByCustom)) {
            self::$settings[$actionName]['varyByCustom'] = $varyByCustom;
        }
    }

    /**
     * Sets the cache duration, in seconds.
     * 
     * @param string $actionName Action name.
     * @param int $value A cache duration value, in seconds.
     * 
     * @return void
     */
    public static function setDuration($actionName, $value) {
        if (!self::canCache($actionName)) { return; }
        self::$settings[$actionName]['duration'] = $value;
    }

    /**
     * Sets the location. See OutputCacheLocation class for a list of possible values.
     * 
     * @param string $actionName Action name.
     * @param int $value A cache location.
     * 
     * @return void
     */
    public static function setLocation($actionName, $value) {
        if (!self::canCache($actionName)) { return; }
        self::$settings[$actionName]['location'] = $value;
    }

    /**
     * Sets the vary-by-param value.
     * 
     * @param string $actionName Action name.
     * @param string $value A semicolon-separated list of strings that correspond to query-string values for the GET method, or to parameter values for the POST method.
     * 
     * @return void
     */
    public static function setVaryByParam($actionName, $value) {
        if (!self::canCache($actionName)) { return; }
        self::$settings[$actionName]['varyByParam'] = $value;
    }

    /**
     * Sets the vary-by-header value.
     * 
     * @param string $actionName Action name.
     * @param string $value A semicolon-separated list of HTTP headers that are used to vary the output cache.
     * 
     * @return void
     */
    public static function setVaryByHeader($actionName, $value) {
        if (!self::canCache($actionName)) { return; }
        self::$settings[$actionName]['varyByHeader'] = $value;
    }

    /**
     * Sets the vary-by-custom.
     * 
     * @param string $actionName Action name.
     * @param callback $value A function to check.
     * For example: function($actionContext) {
     *   $userAgent = $actionContext->getHttpContext()->getRequest()->server('HTTP_USER_AGENT');
     *   $browser = get_browser($userAgent, true);
     * 
     *   if ($browser['browser'] == 'Firefox') {
     *     return true;
     *   }
     *   else {
     *     return false;
     *   }
     * }
     * 
     * @return void
     */
    public static function setVaryByCustom($actionName, $value) {
        if (!self::canCache($actionName)) { return; }
        self::$settings[$actionName]['varyByCustom'] = function($actionContext) use ($value) {
            return $value($actionContext);
        };
    }

    private static function canCache(&$actionName) {
        if (empty($actionName)) {
            $actionName = '.';
        }

        if ($actionName != '.' && !self::$actionContext->actionNameEquals($actionName)) {
            return false;
        }

        $actionName = strtolower($actionName);

        if (!isset(self::$settings[$actionName])) {
            self::$settings[$actionName] = array();
        }

        return true;
    }

}