<?php
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
     *  Gets or sets a value that indicates the value of this SelectListItem.
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

}