<?php

namespace App\IpGeolocation\ApiProvider;

use Symfony\Contracts\HttpClient\ResponseInterface;

class GeopluginProvider extends AbstractProvider {

    public const NAME = 'geoplugin';
    protected const COUNTRY_CODE_KEY = 'geoplugin_countryCode';

    protected function request(string $ip) : ResponseInterface {
        return $this->client->request('GET', 'http://www.geoplugin.net/json.gp', [
            'query' => [
                'ip' => $ip
            ]
        ]);
    }

    protected function checkContent(array $response_content) : void {
        $geoplugin_status = $response_content['geoplugin_status'] ?? null;
        if (null === $geoplugin_status || (200 > $geoplugin_status || 300 <= $geoplugin_status)) {
            throw new \RuntimeException(sprintf('API provider "%s" returned with error', static::NAME));
        }
    }

}