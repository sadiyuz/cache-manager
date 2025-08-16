<?php

namespace App\Cache\Drivers;

class MemoryCacheDriver implements CacheDriver
{
    private $cache = [];

    public function put($key, $value, $minutes = null)
    {
        $this->cache[md5($key)] = [
            'value' => $value,
            'expiration' => $minutes ? time() + ($minutes * 60) : null
        ];
        
        return true;
    }

    public function get($key, $default = null)
    {
        $key = md5($key);
        
        if (!isset($this->cache[$key])) {
            return $default;
        }

        if ($this->cache[$key]['expiration'] && $this->cache[$key]['expiration'] < time()) {
            $this->forget($key);
            return $default;
        }

        return $this->cache[$key]['value'];
    }

    public function has($key)
    {
        $key = md5($key);
        
        if (!isset($this->cache[$key])) {
            return false;
        }

        if ($this->cache[$key]['expiration'] && $this->cache[$key]['expiration'] < time()) {
            $this->forget($key);
            return false;
        }

        return true;
    }

    public function forget($key)
    {
        $key = md5($key);
        
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);
            return true;
        }
        
        return false;
    }

    public function flush()
    {
        $this->cache = [];
        return true;
    }
}