<?php

namespace App\CacheService;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class CacheFactory {

    private const CACHE_SYSTEM_APCU = 'apcu';
    private const CACHE_SYSTEM_MEMCACHED = 'memcached';
    private const CACHE_SYSTEM_FILESYSTEM = 'file';

    private array $instances = [];
    private MemcachedConnectionFactory $memcachedConnectionFactory;

    public function __construct(MemcachedConnectionFactory $memcached_connection_factory) {
        $this->memcachedConnectionFactory = $memcached_connection_factory;
    }

    public function getInstance(string $cache_type, string $cache_namespace = '') : CacheInterface {
        if (empty($this->instances[$cache_type][$cache_namespace])) {
            $this->instances[$cache_type][$cache_namespace] = $this->createInstance($cache_type, $cache_namespace);
        }

        return $this->instances[$cache_type][$cache_namespace];
    }

    private function createInstance(string $cache_system, string $cache_namespace) : CacheInterface {
        switch($cache_system) {
            case static::CACHE_SYSTEM_APCU :       return $this->createInstanceApcu($cache_namespace);
            case static::CACHE_SYSTEM_FILESYSTEM : return $this->createInstanceFilesystem($cache_namespace);
            case static::CACHE_SYSTEM_MEMCACHED :  return $this->createInstanceMemcached($cache_namespace);
            default:
                throw new \LogicException(sprintf('Cache system "%s" not supported', $cache_system));
        }
    }

    private function createInstanceApcu(string $cache_namespace) : ApcuAdapter {
        return new ApcuAdapter($cache_namespace);
    }

    private function createInstanceFilesystem(string $cache_namespace) : FilesystemAdapter {
        return new FilesystemAdapter($cache_namespace);
    }

    private function createInstanceMemcached(string $cache_namespace) : MemcachedAdapter {
        $connection = $this->memcachedConnectionFactory->getConnection();
        return new MemcachedAdapter($connection, $cache_namespace);
    }
}