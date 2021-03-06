<?php

namespace App\IpGeolocation\ApiProvider;

use Symfony\Contracts\HttpClient\ResponseInterface;

class IpApiProvider extends AbstractProvider {

    public const NAME = 'ip-api';
    protected const COUNTRY_CODE_KEY = 'countryCode';

    protected function request(string $ip) : ResponseInterface {
        return $this->client->request('GET', 'http://ip-api.com/json/'.$ip, [
            'query' => [
                'fields' => 'status,countryCode'
            ]
        ]);
    }

    protected function checkContent(array $response_content) : void {
        if (!isset($response_content['status']) || $response_content['status'] !== 'success') {
            throw new \RuntimeException(sprintf('API provider "%s" returned with error', static::NAME));
        }
    }

}