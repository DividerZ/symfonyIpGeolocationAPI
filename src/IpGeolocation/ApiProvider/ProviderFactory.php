<?php

namespace App\IpGeolocation\ApiProvider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProviderFactory {

    private array $instances = [];
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function getInstance(string $provider_name) : ApiProviderInterface {
        if (empty($this->instances[$provider_name])) {
            $this->instances[$provider_name] = $this->createInstance($provider_name);
        }

        return $this->instances[$provider_name];
    }

    private function createInstance(string $provider_name) : ApiProviderInterface {
        switch($provider_name) {
            case IpApiProvider::NAME :      return new IpApiProvider($this->client);
            case IpwhoisProvider::NAME :    return new IpwhoisProvider($this->client);
            case GeopluginProvider::NAME :  return new GeopluginProvider($this->client);
            default:
                throw new \LogicException(sprintf('API provider "%s" not supported', $provider_name));
        }
    }

}