<?php

namespace App\IpGeolocation;

use App\IpGeolocation\ApiProvider\AbstractProvider;
use App\IpGeolocation\ApiProvider\IpApiProvider;
use App\IpGeolocation\ApiProvider\IpwhoisProvider;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProviderFactory {

    private array $instances;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function getInstance(string $provider_name) : AbstractProvider {
        if (empty($this->instances[$provider_name])) {
            $this->instances[$provider_name] = $this->createInstance($provider_name);
        }

        return $this->instances[$provider_name];
    }

    private function createInstance(string $provider_name) : AbstractProvider {
        switch($provider_name) {
            case IpwhoisProvider::NAME :    return new IpwhoisProvider($this->client);
            case IpApiProvider::NAME :      return new IpApiProvider($this->client);
            default:
                throw new \LogicException(sprintf('Провайдер "%s" не поддерживается', $provider_name));
        }
    }

}