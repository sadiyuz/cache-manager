<?php

namespace App\Cache;

use App\Cache\Drivers\FileCacheDriver;
use App\Cache\Drivers\MemoryCacheDriver;

/**
 * Cache Facade class providing a static interface to caching operations
 */
class Cache
{
    private static $instance;
    private $driver;

    /**
     * Initialize cache with specified driver
     */
    private function __construct($driver = null)
    {
        $driverName = $driver ?? 'file';
        
        switch ($driverName) {
            case 'memory':
                $this->driver = new MemoryCacheDriver();
                break;
            default:
                $this->driver = new FileCacheDriver();
        }
    }

    /**
     * Get singleton instance of Cache
     */
    private static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Store an item in the cache
     */
    public static function put($key, $value, $minutes = null)
    {
        return self::getInstance()->driver->put($key, $value, $minutes);
    }

    /**
     * Retrieve an item from the cache
     */
    public static function get($key, $default = null)
    {
        return self::getInstance()->driver->get($key, $default);
    }

    /**
     * Check if an item exists in the cache
     */
    public static function has($key)
    {
        return self::getInstance()->driver->has($key);
    }

    /**
     * Remove an item from the cache
     */
    public static function forget($key)
    {
        return self::getInstance()->driver->forget($key);
    }

    /**
     * Clear all cache
     */
    public static function flush()
    {
        return self::getInstance()->driver->flush();
    }

    /**
     * Get an item from cache or store it if it doesn't exist
     */
    public static function remember($key, $minutes, callable $callback)
    {
        $value = self::get($key);
        
        if (!is_null($value)) {
            return $value;
        }

        $value = $callback();
        self::put($key, $value, $minutes);
        
        return $value;
    }

    /**
     * Set the cache driver
     */
    public static function driver($driver)
    {
        self::$instance = new self($driver);
        return self::$instance;
    }
}