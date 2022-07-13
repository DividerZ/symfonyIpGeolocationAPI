<?php

namespace App\IpGeolocation\ApiProvider;

use App\IpGeolocation\GeolocationIpItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractProvider {

    protected const COUNTRY_CODE_KEY = null;

    protected HttpClientInterface $client;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function get(string $ip) : GeolocationIpItem {
        $response_data = $this->getFromApi($ip);
        $geo_ip_item = new GeolocationIpItem();

        if (static::COUNTRY_CODE_KEY !== null && isset($response_data[static::COUNTRY_CODE_KEY])) {
            $geo_ip_item->setCountryCode($response_data[static::COUNTRY_CODE_KEY]);
        }

        return $geo_ip_item;
    }

    abstract protected function getFromApi(string $ip) : array;

}
