<?php
namespace PhpMvc;

/**
 * Encapsulates the state of model binding to a property of an action-method argument, or to the argument itself.
 */
class ModelState implements \ArrayAccess {

    /**
     * Gets or sets model state entries.
     * 
     * @var ModelStateEntry[]
     */
    public $items = array();

    /**
     * Gets or sets errors.
     * 
     * @var array
     */
    private $errors = array();

    /**
     * Returns true if the state does not contain errors; otherwise, false.
     * 
     * @param string $key If the key is specified, only the specified key will be checked for errors.
     * 
     * @return bool
     */
    public function isValid($key = null) {
        if (isset($key)) {
            return empty($this->errors[$key]);
        }
        else {
            return empty($this->errors);
        }
    }

    /**
     * Returns errors of the model state.
     * 
     * @param string $key If the key is specified, only errors for the specified key will be returned.
     * 
     * @return array
     */
    public function getErrors($key = null) {
        if (isset($key)) {
            return !empty($this->errors[$key]) ? $this->errors[$key] : array();
        }
        else {
            return $this->errors;
        }
    }

    /**
     * Gets the key-value pair.
     * 
     * @return array
     */
    public function getKeyValuePair() {
        $result = array();

        foreach ($this->items as $key => $entry) {
            $result[$key] = $entry->value;
        }

        return $result;
    }

    /**
     * Gets the key sequence.
     * 
     * @return array
     */
    public function getKeys() {
        return array_keys($this->items);
    }

    /**
     * Gets the value sequence.
     * 
     * @return array
     */
    public function getValues() {
        $result = array();

        foreach ($this->items as $key => $entry) {
            $result[] = $entry->value;
        }

        return $result;
    }

    /**
     * Adds the specified errorMessage to the Errors instance that is associated with the specified key.
     * 
     * @param string $key The key of state to add error.
     * @param mixed $error The error or error message to add.
     * 
     * @return void
     */
    public function addError($key, $error) {
        if (array_key_exists($key, $this->errors)) {
            $this->errors[$key][] = $error;
        }
        else {
            $this->errors[$key] = array($error);
        }
    }

    /**
     * Creates an object from the model state entities.
     * 
     * @return object
     */
    public function toObject() {
        return (object)$this->items;
    }

    /**
     * Whether or not an offset exists.
     *
     * @param string|int $offset An offset to check for.
     * 
     * @return bool
     */
    function offsetExists($offset)
    {
      return isset($this->items[$offset]);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset The offset to retrieve.
     * 
     * @return mixed
     */
    function offsetGet($offset)
    {
      return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * 
     * @return void
     */
    function offsetSet($offset, $value)
    {
      $this->items[$offset] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset.
     * 
     * @return void
     */
    function offsetUnset($offset)
    {
      unset($this->items[$offset]);
    }

}