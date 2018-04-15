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