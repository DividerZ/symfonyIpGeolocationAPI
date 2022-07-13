<?php

namespace App\IpGeolocation\ApiProvider;

class IpwhoisProvider extends AbstractProvider {

    public const NAME = 'ipwhois';
    protected const COUNTRY_CODE_KEY = 'country_code';

    protected function getFromApi(string $ip) : array {
        $response = $this->client->request('GET', 'https://ipwho.is/'.$ip, [
            'query' => [
                'fields' => 'success,country_code',
                'output' => 'json'
            ]
        ]);

        $headers = $response->getHeaders();
        if ($response->getStatusCode() !== 200 || !(isset($headers['content-type'][0]) && str_contains($headers['content-type'][0], 'application/json'))) {
            throw new \RuntimeException('Ошибка связи с поставщиком API '.static::NAME);
        }

        $content = $response->toArray();
        if (empty($content['success'])) {
            throw new \RuntimeException(sprintf('Поставщика API %s вернул запрос с ошибкой', static::NAME));
        }
        // вот тут надо переписать ответ - берём имя поля от абстракта и передаём ему массив
        return $content;
    }

}