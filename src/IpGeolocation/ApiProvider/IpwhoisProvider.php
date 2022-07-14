<?php

namespace App\IpGeolocation\ApiProvider;

use Symfony\Contracts\HttpClient\ResponseInterface;

class IpwhoisProvider extends AbstractProvider {

    public const NAME = 'ipwhois';
    protected const COUNTRY_CODE_KEY = 'country_code';

    protected function request(string $ip) : ResponseInterface {
        return $this->client->request('GET', 'https://ipwho.is/'.$ip, [
            'query' => [
                'fields' => 'success,country_code',
                'output' => 'json'
            ]
        ]);
    }

    protected function checkContent(array $response_content) : void {
        if (empty($response_content['success']) || $response_content['success'] !== true) {
            throw new \RuntimeException(sprintf('API provider "%s" returned with error', static::NAME));
        }
    }

}