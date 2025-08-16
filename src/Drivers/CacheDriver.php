<?php

namespace App\Cache\Drivers;

/**
 * Interface for cache drivers
 */
interface CacheDriver
{
    public function put($key, $value, $minutes = null);
    public function get($key, $default = null);
    public function has($key);
    public function forget($key);
    public function flush();
}