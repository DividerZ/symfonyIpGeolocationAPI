<?php

namespace App\CacheService;

use Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MemcachedConnectionFactory {

    private ?Memcached $clientConnection = null;
    private string $host;
    private int $port;

    public function __construct(ContainerBagInterface $app_params) {
        $this->host = $app_params->get('memcached.host');
        $this->port = $app_params->get('memcached.port');
    }

    public function getConnection() : Memcached {
        if (null === $this->clientConnection) {
            $this->clientConnection = $this->createConnection();
        }

        return $this->clientConnection;
    }

    private function createConnection() : Memcached {
        $connection_link = sprintf('memcached://%s:%d', $this->host, $this->port);
        return MemcachedAdapter::createConnection($connection_link);
    }
}