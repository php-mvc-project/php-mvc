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
 * Represents segment of route.
 */
class RouteSegment {

    /**
     * Segment name.
     * 
     * @var string
     */
    public $name;

    /**
     * Regular expressions pattern.
     * 
     * @var string
     */
    public $pattern;

    /**
     * Indicates that the parameter is optional..
     * 
     * @var bool
     */
    public $optional;

    /**
     * Default value.
     * 
     * @return string
     */
    public $default;

    /**
     * Before text.
     * 
     * @var string
     */
    public $before;

    /**
     * After text.
     * 
     * @var string
     */
    public $after;

    /**
     * Glue to the previous segment.
     * 
     * @var bool
     */
    public $glued;

    /**
     * Is pre-end.
     * 
     * @var bool
     */
    public $preEnd;

    /**
     * The end :)
     * 
     * @var bool
     */
    public $end;

}