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
     * Gets or sets metadata of Model.
     * 
     * @var ModelDataAnnotation[]
     */
    public $annotations = array();

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
        if (is_array($key)) {
            $key = implode('_', $key);
        }

        if (array_key_exists($key, $this->errors)) {
            $this->errors[$key][] = $error;
        }
        else {
            $this->errors[$key] = array($error);
        }
    }

    /**
     * Gets the metadata for the specified model key.
     * If there is no data, it returns null.
     * 
     * @param string|string[] @key The key to get metadata.
     * 
     * @return ModelDataAnnotation|null
     */
    public function getAnnotation($key) {
        if (is_array($key)) {
            $key = implode('_', $key);
        }

        if (array_key_exists($key, $this->annotations)) {
            return $this->annotations[$key];
        }
        else {
            return null;
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