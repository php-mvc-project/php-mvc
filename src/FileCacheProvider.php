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
 * Represents a standard cache provider for storing the cache in the file system.
 */
final class FileCacheProvider implements CacheProvider {

    /**
     * The path to the cache files directory.
     * 
     * @var string
     */
    private $cachePath;

    /**
     * The timeout of access to cache data, in milliseconds.
     * 
     * @var int
     */
    private $accessTime;

    /**
     * Hash algorithm.
     * 
     * @var string|callback
     */
    private $hash;

    /**
     * Defines cache settings.
     * 
     * @var FileCacheProviderConfig
     */
    private $config;

    /**
     * Initializes a new instance of the FileCacheProvider class.
     * 
     * @param FileCacheProviderConfig $config Cache settings.
     */
    public function __construct($config = null) {
        if (!isset($config)) {
            $config = new FileCacheProviderConfig();
        }
        elseif (is_array($config)) {
            $configArray = $config;
            $config = null;
            InternalHelper::arrayToObject($configArray, $config, '\PhpMvc\FileCacheProviderConfig');
        }

        if (!isset($config->cachePath) || empty($config->cachePath)) {
            $config->cachePath = '~/cache';
        }

        $this->config = $config;
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
        $this->getCacheFilePaths($key, $regionName, $policy, $cache);
        $this->addCache($policy, $cache, $value, $duration);
    }

    /**
     * Removes all cache entries.
     * 
     * @param string $regionName A name of region.
     * 
     * @return int Number of deleted records.
     */
    public function clear($regionName = null) {
        $path = $this->getPath($regionName);
        $result = count(glob($path . DIRECTORY_SEPARATOR . '*.cache'));

        array_map('unlink', glob($path . DIRECTORY_SEPARATOR . '*.cache'));
        array_map('unlink', glob($path . DIRECTORY_SEPARATOR . '*.policy'));

        return $result;
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
        return $this->containsFiles($key, $regionName);
    }

    /**
     * Gets the total number of cache entries in the cache.
     * 
     * @param string $regionName A name of region.
     * 
     * @return int
     */
    public function count($regionName = null) {
        return count(glob($this->getPath($regionName) . DIRECTORY_SEPARATOR . '*.cache'));
    }

    /**
     * Initializes the provider.
     * 
     * @return void
     */
    public function init() {
        $this->cachePath = PathUtility::mapPath($this->config->cachePath);
        $this->accessTime = (isset($this->config->accessTime) ? (int)$this->config->accessTime : 100) * 1000;
        $this->hash = (isset($this->config->hash) ? $this->config->hash : null);
    }

    /**
     * Gets the specified cache entry from the cache as an object.
     * 
     * @param string $key A unique identifier for the cache entry.
     * @param string $regionName A name of region.
     * 
     * @return mixed|null
     */
    public function get($key, $regionName = null) {
        if ($this->containsFiles($key, $regionName, $policy, $cache)) {
            $this->getCacheContent($policy, $cache, $result);

            return $result;
        }
        else {
            return null;
        }
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
        if ($this->containsFiles($key, $regionName, $policy, $cache) && $this->getCacheContent($policy, $cache, $result) === true) {
            return $result;
        }
        else {
            return $this->addCache($policy, $cache, $value, $duration);
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
        if ($this->containsFiles($key, $regionName, $policy, $cache)) {
            if ($this->getCacheContent($policy, $cache, $result) === true) {
                $this->tryDeleteFile($policy);
                $this->tryDeleteFile($cache);
            }

            return $result;
        }
        else {
            return null;
        }
    }

    /**
     * Gets normalized key.
     * 
     * @param string $value The key to proccessing.
     * 
     * @return string
     */
    private function getKey($value) {
        return !empty($this->hash) ? call_user_func($this->hash, $value) : preg_replace('/[^0-9a-z\.\_\-]/i', '', $value);
    }

    /**
     * Gets directory path of cache storage.
     * 
     * @return string
     */
    private function getPath($regionName = null) {
        $cachePath = $this->cachePath;

        if (!empty($regionName)) {
            $cachePath .= DIRECTORY_SEPARATOR . $regionName;
        }

        if (!is_dir($cachePath)) {
            if (!mkdir($cachePath, 0775, true)) {
                throw new \Exception('Unable to create cache directory "' . $cachePath . '".');
            }
        }
        elseif (!is_readable($cachePath) || !is_writable($cachePath)) {
            if (!chmod($this->getCachePath(), 0775)) {
                throw new \Exception('The path "' . $cachePath . '" must be readable and writeable.');
            }
        }

        return $cachePath;
    }

    /**
     * Gets path to cache and policy files.
     * 
     * @return void
     */
    private function getCacheFilePaths($key, $regionName, &$policy, &$cache) {
        $key = $this->getKey($key);
        $path = $this->getPath($regionName);
        $policy = $path . DIRECTORY_SEPARATOR . $key . '.policy';
        $cache = $path . DIRECTORY_SEPARATOR . $key . '.cache';
    }

    /**
     * Checks whether the cache entry already exists in the cache.
     */
    private function containsFiles($key, $regionName, &$policy = null, &$cache = null) {
        $this->getCacheFilePaths($key, $regionName, $policy, $cache);

        if (!is_file($policy) && !is_file($cache)) {
            return false;
        }
        elseif (!is_file($policy) && is_file($cache)) {
            $this->tryDeleteFile($cache);
        }
        elseif (is_file($policy) && !is_file($cache)) {
            $this->tryDeleteFile($policy);
        }

        return true;
    }

    /**
     * Gets content of cache entry.
     * 
     * @return bool
     */
    private function getCacheContent($policy, $cache, &$data) {
        $policyContent = $this->tryReadFile($policy);
        $cacheContent = $this->tryReadFile($cache);
        $data = null;

        if ($policyContent === false || $cacheContent === false) {
            $this->tryDeleteFile($policy);
            $this->tryDeleteFile($cache);
            return false;
        }

        $policyEntry = json_decode($policyContent, true);

        $absoluteExpiration = (int)$policyEntry['absoluteExpiration'];

        if ($absoluteExpiration != 0 && $absoluteExpiration < time()) {
            $this->tryDeleteFile($policy);
            $this->tryDeleteFile($cache);
            return false;
        }

        $data = unserialize($cacheContent);

        return true;
    }

    /**
     * Saves cache.
     */
    private function addCache($policy, $cache, $value, $duration) {
        $content = is_callable($value) ? $value() : $value;

        $policyContent = json_encode(array('absoluteExpiration' => $duration > 0 ? time() + $duration : 0));

        if ($this->tryWriteFile($policy, $policyContent) === true) {
            if (!$this->tryWriteFile($cache, serialize($content))) {
                $this->tryDeleteFile($policy);
            }
        }

        return $content;
    }

    /**
     * Trying to delete the specified file.
     * 
     * @param string $path The path to file to remove.
     * 
     * @return bool
     */
    private function tryDeleteFile($path) {
        $interval = 100000;
        $totalTime = 0;

        while (file_exists($path) && !unlink($path)) {
            if ($totalTime >= $this->accessTime) {
                return false;
            }

            usleep($interval);

            $totalTime += $interval;
        }

        return true;
    }

    /**
     * Trying to read the file.
     * 
     * @param string $path The path to the file to be read..
     * 
     * @return string|bool
     */
    private function tryReadFile($path) {
        if (!file_exists($path)) {
            return false;
        }

        $interval = 100000;
        $totalTime = 0;

        while (($result = file_get_contents($path)) === false) {
            if ($totalTime >= $this->accessTime) {
                return false;
            }

            usleep($interval);

            $totalTime += $interval;
        }

        return $result;
    }

    /**
     * Trying to save the file.
     * 
     * @return bool
     */
    private function tryWriteFile($path, $content) {
        $interval = 100000;
        $totalTime = 0;

        while (file_put_contents($path, $content) === false) {
            if ($totalTime >= $this->accessTime) {
                return false;
            }

            usleep($interval);

            $totalTime += $interval;
        }

        return true;
    }

}