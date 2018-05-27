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
 * Represents an item (<option>) in a HTML <select>.
 */
class SelectListItem {

    /**
     * Gets or sets a value that indicates the display text of this SelectListItem.
     * 
     * @var string
     */
    public $text;

    /**
     *  Gets or sets a value of this SelectListItem.
     * 
     * @var string
     */
    public $value;

    /**
     * Gets or sets a value that indicates whether this SelectListItem is selected.
     * 
     * @var bool
     */
    public $selected;

    /**
     * Gets or sets a value that indicates whether this SelectListItem is disabled.
     * 
     * @var bool
     */
    public $disabled;

    /**
     * Represents the optgroup HTML element this item is wrapped into.
     * In a select list, multiple groups with the same name are supported.
     * They are compared with reference equality.
     * 
     * @var SelectListGroup
     */
    public $group;

    /**
     * Initializes a new instance of the SelectListItem.
     * 
     * @param string $text The display text of this SelectListItem.
     * @param string $value The value of this SelectListItem.
     * @param bool $selected The value that indicates whether this SelectListItem is selected.
     * @param bool $disabled The value that indicates whether this SelectListItem is disabled.
     * @param SelectListGroup $group Represents the optgroup HTML element this item is wrapped into.
     */
    public function __construct($text = null, $value = null, $selected = false, $disabled = false, $group = null) {
        $this->text = $text;
        $this->value = $value;
        $this->selected = $selected;
        $this->disabled = $disabled;
        $this->group = $group;
    }

}