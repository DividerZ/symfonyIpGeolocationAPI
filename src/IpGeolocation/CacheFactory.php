<?php

namespace App\IpGeolocation;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class CacheFactory {

    private const CACHE_SYSTEM_APCU = 'apcu';
    private const CACHE_SYSTEM_MEMCACHED = 'memcached';
    private const CACHE_SYSTEM_FILESYSTEM = 'file';
    private const CACHE_NAMESPACE = 'ipToLocation';

    private array $instances = [];

    public function __construct() {}

    public function getInstance(string $cache_type) : CacheInterface {
        if (empty($this->instances[$cache_type])) {
            $this->instances[$cache_type] = $this->createInstance($cache_type);
        }

        return $this->instances[$cache_type];
    }

    private function createInstance(string $cache_system) : CacheInterface {
        switch($cache_system) {
            case static::CACHE_SYSTEM_APCU :       return $this->createInstanceApcu();
            case static::CACHE_SYSTEM_FILESYSTEM : return $this->createInstanceFilesystem();
            case static::CACHE_SYSTEM_MEMCACHED :  return $this->createInstanceMemcached();
            default:
                throw new \LogicException(sprintf('Система кеширования "%s" не поддерживается', $cache_system));
        }
    }

    private function createInstanceApcu() : ApcuAdapter {
        return new ApcuAdapter(static::CACHE_NAMESPACE);
    }

    private function createInstanceFilesystem() : FilesystemAdapter {
        return new FilesystemAdapter(static::CACHE_NAMESPACE);
    }

    private function createInstanceMemcached() : MemcachedAdapter {
        $memcached_client = MemcachedAdapter::createConnection('memcached://localhost:11211');
        return new MemcachedAdapter($memcached_client, static::CACHE_NAMESPACE);
    }
}