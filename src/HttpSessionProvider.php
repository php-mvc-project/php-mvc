<?php
namespace PhpMvc;

/**
 * Defines the contract that represents the session state provider.
 */
interface HttpSessionProvider extends \ArrayAccess, \Iterator {

    /**
     * Discards session array changes and finish session.
     * 
     * @return bool
     */
    function abort();

    /**
     * Adds a new item to the session-state collection.
     * 
     * @param string $name The name of the item to add to the session-state collection.
     * @param mixed $value The value of the item to add to the session-state collection.
     * 
     * @return bool
     */
    function add($name, $value);

    /**
     * Clears all values from the session-state item collection.
     * 
     * @return bool
     */
    function clear();

    /**
     * Writes session data and end session.
     * 
     * @return bool
     */
    function commit();

    /**
     * Gets or sets the session cookie parameters.
     * 
     * @param array $param If params is specified, it will replace the current cookie params.
     * 
     * @return array|bool
     */
    function cookieParams($params = null);

    /**
     * Creates a new session id.
     * 
     * @param string $prefix If prefix is specified, new session id is prefixed by prefix.
     * 
     * @return string
     */
    function createId($prefix = null);

    /**
     * Decodes session data from a session encoded string.
     * 
     * @param string $data The encoded data to be stored.
     * 
     * @return bool
     */
    function decode($data);

    /**
     * Encodes the current session data as a session encoded string.
     * 
     * @return string
     */
    function encode();

    /**
     * Perform session data garbage collection.
     * 
     * @return int
     */
    function gc();

    /**
     * Gets or sets a session value by name.
     * 
     * @param string $name The key name of the session value.
     * 
     * @return mixed
     */
    function get($name);

    /**
     * Initializes the provider.
     * 
     * @return void
     */
    function init();

    /**
     * Gets or sets the current session repository.
     * 
     * @param string $value If value is specified, it will replace the current session repository.
     * 
     * @return string
     */
    function repository($value = null);

    /**
     * Re-initialize session array with original values.
     * 
     * @return bool
     */
    function reset();

    /**
     * Gets and/or sets the current session id.
     * 
     * @param string $id If id is specified, it will replace the current session id.
     * 
     * @return string
     */
    function sessionId($id = null);

    /**
     * Gets and/or sets the current session name.
     * 
     * @param string $name If name is specified, it will replace the current session name. 
     * 
     * @return string Returns the name of the current session.
     * If name is given and function updates the session name, name of the old session is returned.
     */
    function sessionName($name = null);

    /**
     * Returns the current session status.
     * 
     * * \PHP_SESSION_DISABLED - if sessions are disabled.
     * * \PHP_SESSION_NONE - if sessions are enabled, but none exists.
     * * \PHP_SESSION_ACTIVE - if sessions are enabled, and one exists.
     * 
     * @return int
     */
    function status();

}