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
 * Represents the optgroup HTML element and its attributes.
 */
class SelectListGroup {

    /**
     * Gets or sets a value that indicates whether this SelectListGroup is disabled.
     * 
     * @var bool
     */
    public $disabled;

    /**
     * Represents the value of the optgroup's label.
     * 
     * @var string
     */
    public $name;

    /**
     * Initializes a new instance of the SelectListGroup.
     * 
     * @param string $name The value of the optgroup's label.
     * @param bool $disabled The value that indicates whether this SelectListGroup is disabled.
     */
    public function __construct($name = null, $disabled = false) {
        $this->name = $name;
        $this->disabled = $disabled;
    }

}