<?php

namespace App\IpGeolocation\ApiProvider;

class IpApiProvider extends AbstractProvider {

    public const NAME = 'ip-api';
    protected const COUNTRY_CODE_KEY = 'countryCode';

    protected function getFromApi(string $ip) : array {
        $response = $this->client->request('GET', 'http://ip-api.com/json/'.$ip, [
            'query' => [
                'fields' => 'status,countryCode'
            ]
        ]);

        $headers = $response->getHeaders();
        if ($response->getStatusCode() == 200 && (isset($headers['content-type'][0]) && str_contains($headers['content-type'][0], 'application/json'))) {
            $content = $response->toArray();
            if (isset($content['status']) && $content['status'] === 'success') {
                return $content;
            }
        }
        throw new \RuntimeException('Поставщик API вернул некорректные данные');
    }

}