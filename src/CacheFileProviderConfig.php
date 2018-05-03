<?php
namespace PhpMvc;

/**
 * Represents a standard cache provider for storing the cache in the file system.
 */
final class CacheFileProviderConfig {

    /**
     * The path to the cache files directory.
     * 
     * @var string
     */
    public $cachePath;

    /**
     * The timeout of access to cache data, in milliseconds.
     * 
     * @var int
     */
    public $accessTime;

    /**
     * Hash algorithm.
     * 
     * @var string|callback
     */
    public $hash;

}