<?php

namespace App\IpGeolocation;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Validator\Constraints as Assert;

class IpGeolocationService {

    private ValidatorInterface $validator;
    private ProviderFactory $provider_factory;
    private CacheFactory $cacheFactory;
    private string $cache_system;
    private string $provider_name;
    private int $cache_lifetime;

    public function __construct(ValidatorInterface $validator, ProviderFactory $provider_factory, CacheFactory $cache_factory, ContainerBagInterface $app_params) {
        $this->validator = $validator;
        $this->provider_factory = $provider_factory;
        $this->cacheFactory = $cache_factory;
        $this->cache_system = $app_params->get('ip_geolocation.cache_system');
        $this->cache_lifetime = $app_params->get('ip_geolocation.cache_lifetime');
        $this->provider_name = $app_params->get('ip_geolocation.provider_name');
    }

    public function get(string $ip) : GeolocationIpItem {
        if (!$this->isValidIp($ip)) {
            throw new \RuntimeException('первый аргумент не является ip-адресом');
        }

        $data_version = 1.0;
        $cache = $this->cacheFactory->getInstance($this->cache_system);
        $api_provider = $this->provider_factory->getInstance($this->provider_name);
        $geo_ip_item = $cache->get($ip, function(ItemInterface $item) use ($ip, $api_provider) {
            $item->expiresAfter($this->cache_lifetime);
            return $api_provider->get($ip);
        }, $data_version);

        return $geo_ip_item;
    }

    protected function isValidIp(string $ip) : bool {
        $violation_list = $this->validator->validate($ip, new Assert\Ip(
            null,
            Assert\Ip::ALL
        ));

        return $violation_list->count() === 0;
    }
}