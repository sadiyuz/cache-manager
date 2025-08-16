<?php

namespace App\Cache\Drivers;

class FileCacheDriver implements CacheDriver
{
    private $cachePath;

    public function __construct()
    {
        $this->cachePath = sys_get_temp_dir() . '/cache/';
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function put($key, $value, $minutes = null)
    {
        $expiration = $minutes ? time() + ($minutes * 60) : null;
        $data = [
            'value' => serialize($value),
            'expiration' => $expiration
        ];
        
        file_put_contents(
            $this->cachePath . md5($key),
            serialize($data)
        );
        
        return true;
    }

    public function get($key, $default = null)
    {
        $file = $this->cachePath . md5($key);
        
        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));
        
        if ($data['expiration'] && $data['expiration'] < time()) {
            $this->forget($key);
            return $default;
        }

        return unserialize($data['value']);
    }

    public function has($key)
    {
        $file = $this->cachePath . md5($key);
        
        if (!file_exists($file)) {
            return false;
        }

        $data = unserialize(file_get_contents($file));
        
        if ($data['expiration'] && $data['expiration'] < time()) {
            $this->forget($key);
            return false;
        }

        return true;
    }

    public function forget($key)
    {
        $file = $this->cachePath . md5($key);
        
        if (file_exists($file)) {
            unlink($file);
            return true;
        }
        
        return false;
    }

    public function flush()
    {
        $files = glob($this->cachePath . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
}