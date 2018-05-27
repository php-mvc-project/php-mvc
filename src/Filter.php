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