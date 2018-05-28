<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

        if ((strpos($path, '/') !== false || strpos($path, '\\') !== false) && is_file($result = $path)) {
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