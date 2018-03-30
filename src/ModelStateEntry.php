<?php
namespace PhpMvc;

/**
 * Represents an entry in a ModelState.
 */
class ModelStateEntry {

    /**
     * Gets or sets the key for this entry.
     * 
     * @var string
     */
    public $key;

    /**
     * Gets or sets the value for this entry.
     * 
     * @var mixed
     */
    public $value;

    /**
     * Get or sets the validation state for this entry.
     * 
     * @var bool|null
     */
    public $validationState;
   
    /**
     * Initializes a new instance of ModelStateEntry with the specified key and value.
     * 
     * @param string $key The key of element.
     * @param string $value The value of element.
     */
    public function __construct($key, $value) {
        $this->key = $key;
        $this->value = $value;
        $this->validationState = null;
    }

}