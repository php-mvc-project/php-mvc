<?php
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