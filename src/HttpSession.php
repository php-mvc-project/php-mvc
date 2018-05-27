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
 * Represents session of the current request.
 * 
 * @see http://php.net/manual/ru/book.session.php
 */
final class HttpSession implements HttpSessionProvider {

    /**
     * @var array
     */
    private $config = array();

    /**
     * Initializes a new instance of the HttpSession for the current request.
     * 
     * @param array $config If provided, this is an associative array of options that will override the currently set session configuration directives.
     * http://php.net/manual/en/session.configuration.php
     */
    public function __construct($config = array()) {
        $this->config = $config;
    }

    /**
     * Discards session array changes and finish session.
     * 
     * @return bool
     */
    public function abort() {
        return session_abort();
    }

    /**
     * Adds a new item to the session-state collection.
     * 
     * @param string $name The name of the item to add to the session-state collection.
     * @param mixed $value The value of the item to add to the session-state collection.
     * 
     * @return bool
     */
    public function add($name, $value) {
        $_SESSION[$name] = $value;
        return true;
    }

    /**
     * Clears all values from the session-state item collection.
     * 
     * @return bool
     */
    public function clear() {
        return session_unset();
    }

    /**
     * Writes session data and end session.
     * 
     * @return bool
     */
    public function commit() {
        return session_write_close();
    }

    /**
     * Gets or sets the session cookie parameters.
     * 
     * @param array $param If params is specified, it will replace the current cookie params.
     * 
     * @return array|bool
     */
    public function cookieParams($params = null) {
        if ($params === null) {
            return session_get_cookie_params();
        }
        else {
            session_set_cookie_params($params);
        }
    }

    /**
     * Creates a new session id.
     * 
     * @param string $prefix If prefix is specified, new session id is prefixed by prefix.
     * 
     * @return string
     */
    public function createId($prefix = null) {
        return session_create_id($prefix);
    }

    /**
     * Decodes session data from a session encoded string.
     * 
     * @param string $data The encoded data to be stored.
     * 
     * @return bool
     */
    public function decode($data) {
        return session_decode($data);
    }

    /**
     * Encodes the current session data as a session encoded string.
     * 
     * @return string
     */
    public function encode() {
        return session_encode();
    }

    /**
     * Perform session data garbage collection.
     * 
     * @return int
     */
    public function gc() {
        return session_gc();
    }

    /**
     * Gets or sets a session value by name.
     * 
     * @param string $name The key name of the session value.
     * 
     * @return mixed
     */
    public function get($name) {
        return $_SESSION[$name];
    }

    /**
     * Initializes the provider.
     * 
     * @return void
     */
    public function init() {
        session_start($this->config);
    }

    /**
     * Gets or sets the current session repository.
     * 
     * @param string $value If value is specified, it will replace the current session repository.
     * 
     * @return string
     */
    public function repository($value = null) {
        return session_save_path($value);
    }

    /**
     * Re-initialize session array with original values.
     * 
     * @return bool
     */
    public function reset() {
        return session_reset();
    }

    /**
     * Gets and/or sets the current session id.
     * 
     * @param string $id If id is specified, it will replace the current session id. 
     * 
     * @return string
     */
    public function sessionId($id = null) {
        return session_id($id);
    }

    /**
     * Gets and/or sets the current session name.
     * 
     * @param string $name If name is specified, it will replace the current session name. 
     * 
     * @return string Returns the name of the current session.
     * If name is given and function updates the session name, name of the old session is returned.
     */
    public function sessionName($name = null) {
        return session_name($name);
    }

    /**
     * Returns the current session status.
     * 
     * * \PHP_SESSION_DISABLED - if sessions are disabled.
     * * \PHP_SESSION_NONE - if sessions are enabled, but none exists.
     * * \PHP_SESSION_ACTIVE - if sessions are enabled, and one exists.
     * 
     * @return int
     */
    public function status() {
        return session_status();
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
      return isset($_SESSION[$offset]);
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
      return isset($_SESSION[$offset]) ? $_SESSION[$offset] : null;
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
        $_SESSION[$offset] = $value;
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
      unset($_SESSION[$offset]);
    }

    /**
     * Rewinds the Iterator to the first element.
     * 
     * @return void
     */
    public function rewind() {
        reset($_SESSION);
    }

    /**
     * Returns the current element.
     * 
     * @return mixed
     */
    public function current() {
        return current($_SESSION);
    }

    /**
     * Returns the key of the current element
     * 
     * @return int
     */
    public function key() {
        return key($_SESSION);
    }

    /**
     * Move forward to next element
     * 
     * @return void
     */
    public function next() {
        next($_SESSION);
    }

    /**
     * Checks if current position is valid.
     * 
     * @return bool
     */
    public function valid() {
        return key($_SESSION) !== null;
    }

}