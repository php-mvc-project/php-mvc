<?php
namespace PhpMvc;

/**
 * Represents a route.
 */
class Route {

    /**
     * 
     * @var string
     */
    public $name;

    /**
     * 
     * @var string
     */
    public $template;

    /**
     * 
     * @var array
     */
    public $defaults;

    /**
     * @var array
     */
    public $constraints;

    /**
     * @var array
     */
    public $values;

    /**
     * Get value or default.
     * 
     * @param string $key The key whose value is to try to get.
     * @param string $default Default value. Only if the value in the $defaults property is not found.
     * 
     * @return string
     */
    public function getValueOrDefault($key, $default = null) {
        if (!empty($this->values[$key])) {
            return $this->values[$key];
        }
        elseif (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }
        else {
            return $default;
        }
    }

}