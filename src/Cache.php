<?php
namespace PhpMvc;

/**
 * Represents the cache.
 */
final class Cache {

    /**
     * Gets or sets cache provider.
     * 
     * @var CacheProvider
     */
    private $provider = null;

    /**
     * Initializes a new instance of the Cache class.
     * 
     * @param CacheProvider $provider The cache provider instance.
     */
    public function __construct($provider) {
        if (!isset($provider) || !$provider instanceof CacheProvider) {
            throw new \Exception('The type of $provider must not be null and must implement the "\PhpMvc\CacheProvider".');
        }

        $this->provider = $provider;
    }

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
        $this->provider->add($key, $value, $duration, $regionName);
    }

    /**
     * Removes all cache entries.
     * 
     * @param string $regionName A name of region.
     * 
     * @return int Number of deleted records.
     */
    public function clear($regionName = null) {
        return $this->provider->clear($regionName);
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
        return $this->provider->contains($key, $regionName);
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
        return $this->provider->get($key, $regionName);
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
        return $this->provider->getOrAdd($key, $value, $duration, $regionName);
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
        return $this->provider->remove($key, $regionName);
    }

}