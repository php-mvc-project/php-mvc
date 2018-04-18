<?php
namespace PhpMvc;

/**
 * Represents query string.
 */
final class QueryString implements \ArrayAccess, \Iterator {

    /**
     * Index of the current item.
     * 
     * @var int
     */
    private $position = 0;

    /**
     * List of items.
     * 
     * @var array
     */
    private $items = array();

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

    /**
     * Rewinds the Iterator to the first element.
     * 
     * @return void
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Returns the current element.
     * 
     * @return string
     */
    public function current() {
        return $this->items[$this->position];
    }

    /**
     * Returns the key of the current element
     * 
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * Move forward to next element
     * 
     * @return void
     */
    public function next() {
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     * 
     * @return bool
     */
    public function valid() {
        return isset($this->items[$this->position]);
    }

    /**
     * Returns query string.
     * 
     * @return string
     */
    public function __toString () {
        return http_build_query($this->items);
    }

}