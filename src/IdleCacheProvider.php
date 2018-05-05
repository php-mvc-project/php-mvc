<?php
namespace PhpMvc;

/**
 * Represents a cache provider that does nothing.
 */
final class IdleCacheProvider implements CacheProvider {

    /**
     * Inserts a cache entry into the cache.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param mixed|callback $value The value to insert.
     * @param int $duration The duration of storage the cache entry (in seconds).
     * @param string $regionName A named region in the cache to which the cache entry can be added.
     * 
     * @return void
     */
    public function add($key, $value, $duration, $regionName = null) {
    }

    /**
     * Removes all cache entries.
     * 
     * @param string $regionName A name of region.
     * 
     * @return int Number of deleted records.
     */
    public function clear($regionName = null) {
        return 0;
    }

    /**
     * Checks whether the cache entry already exists in the cache.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param string $regionName A name of region.
     * 
     * @return bool
     */
    public function contains($key, $regionName = null) {
        return false;
    }

    /**
     * Gets the total number of cache entries in the cache.
     * 
     * @param string $regionName A name of region.
     * 
     * @return int
     */
    public function count($regionName = null) {
        return 0;
    }

    /**
     * Initializes the provider.
     * 
     * @return void
     */
    public function init() {
    }

    /**
     * Gets the specified cache entry from the cache as an object.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param string $regionName A name of region.
     * 
     * @return mixed
     */
    public function get($key, $regionName = null) {
        return null;
    }

    /**
     * Gets a cache entry, or adds it if there is no cache entry.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param mixed|callback $value The value to insert.
     * @param int $duration The duration of storage the cache entry (in seconds).
     * @param string $regionName A name of region.
     * 
     * @return mixed
     */
    public function getOrAdd($key, $value, $duration, $regionName = null) {
        if (is_callable($value) === true) {
            return $value();
        }
        else {
            return $value;
        }
    }

    /**
     * Removes the cache entry from the cache.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param string $regionName A name of region.
     * 
     * @return mixed An object that represents the value of the removed cache entry that was specified by the key,
     * or null if the specified entry was not found.
     */
    public function remove($key, $regionName = null) {
        return null;
    }

}