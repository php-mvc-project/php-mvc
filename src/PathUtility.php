<?php
namespace PhpMvc;

/**
 * Provides utility methods for path operations.
 */
final class PathUtility {

    /**
     * Converts a virtual path to an physical path.
     * 
     * @param string $virtualPath The virtual path to convert to physical path.
     * 
     * @return string
     */
    public static function mapPath($virtualPath = null) {
        if (empty($virtualPath)) {
            return PHPMVC_ROOT_PATH;
        }

        if ($virtualPath[0] == '~' || $virtualPath[0] == '/') {
            $result = PHPMVC_ROOT_PATH;
            $result .= trim(substr($virtualPath, 1, strlen($virtualPath) - 1), '\\/');
        }
        elseif ($virtualPath[0] == '.') {
            $result = realpath(PHPMVC_ROOT_PATH . $virtualPath);
        }
        else {
            $result = PHPMVC_ROOT_PATH . $virtualPath;
        }

        return self::normalizePathSeparators($result);
    }

    /**
     * Checks whether the specified path is relative or not.
     * 
     * @param bool $path Path to check.
     * 
     * @return bool
     */
    public static function isRelativePath($path) {
        if ($path[0] == '~' || $path[0] == '.') {
            return true;
        }

        if ($path[0] == '/' && !is_file($path) && !is_dir($path)) {
            return true;
        }

        return false;
    }

    /**
     * Normalizes slashes in the specified path.
     * 
     * @param string $path The path with crazy slashes :P
     * 
     * @return string
     */
    private static function normalizePathSeparators($path)
    {
        $slash = (PHPMVC_DS == '/' ? '\\' : '/');

        return str_replace($slash, PHPMVC_DS, $path);
    }

    /**
     * Appends the .php extension to the specified path.
     * If the extension already exists, returns FALSE.
     * 
     * @param string $path Path to processing.
     * 
     * @return string|bool
     */
    public static function appendPhpExtension($path) {
        if (strlen($path) >= 4 && substr($path, -4) != '.php') {
            return $path . '.php';
        }

        return false;
    }

    /**
     * Returns the physical path to an existing file.
     * If no path is found, returns FALSE.
     * 
     * @param string $path Relative or physical path.
     * If no file is found on the specified path, an attempt will be made to add the extension .php to the specified path.
     * 
     * @return string|bool
     */
    public static function getFilePath($path) {
        if (empty($path)) {
            return false;
        }

        if (is_file($path)) {
            return $path;
        }

        if (is_file($result = self::mapPath($path))) {
            return $result;
        }

        if (($result = self::appendPhpExtension($path)) !== false) {
            return self::getFilePath($result);
        }

        return false;
    }

    /**
     * Searches for a file with a specified name and returns the correct path.
     * If the file is not found, returns FALSE. 
     * 
     * @param string $path The file name or file path.
     * 
     * @return string|bool
     */
    public static function getViewFilePath($path) {
        if (empty($path)) {
            return false;
        }

        if (is_file($result = $path)) {
            return $result;
        }

        if (self::isRelativePath($path) && is_file($result = self::mapPath($path))) {
            return $result;
        }

        if (is_file($result = PHPMVC_VIEW_PATH . PHPMVC_VIEW . PHPMVC_DS . $path)) {
            return $result;
        }
       
        if (is_file($result = PHPMVC_SHARED_PATH . $path)) {
            return $result;
        }

        if (($result = self::appendPhpExtension($path)) !== false) {
            return self::getViewFilePath($result);
        }

        return false;
    }

}