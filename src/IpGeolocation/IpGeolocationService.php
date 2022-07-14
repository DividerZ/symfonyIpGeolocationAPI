<?php

namespace App\IpGeolocation;

use App\CacheService\CacheFactory;
use App\IpGeolocation\ApiProvider\ProviderFactory;
use App\IpGeolocation\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;

class IpGeolocationService {

    private const CACHE_NAMESPACE = 'IpGeolocationAPI';

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

    public function get(string $ip) : IpGeolocationItem {
        if (!$this->isValidIp($ip)) {
            throw new InvalidArgumentException('first argument is not an public ip address');
        }

        $cache = $this->cacheFactory->getInstance($this->cache_system, static::CACHE_NAMESPACE);
        $api_provider = $this->provider_factory->getInstance($this->provider_name);
        return $cache->get($ip, function(ItemInterface $item) use ($ip, $api_provider) {
            $item->expiresAfter($this->cache_lifetime);
            return $api_provider->get($ip);
        });
    }

    protected function isValidIp(string $ip) : bool {
        $violation_list = $this->validator->validate($ip, new Assert\Ip(
            null,
            Assert\Ip::ALL_ONLY_PUBLIC
        ));

        return $violation_list->count() === 0;
    }
}