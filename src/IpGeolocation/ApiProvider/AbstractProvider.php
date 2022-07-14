<?php

namespace App\IpGeolocation\ApiProvider;

use App\IpGeolocation\IpGeolocationItem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractProvider implements ApiProviderInterface {

    protected const COUNTRY_CODE_KEY = null;

    protected HttpClientInterface $client;

    abstract protected function request(string $ip) : ResponseInterface;
    abstract protected function checkContent(array $response_content) : void;

    public function __construct(HttpClientInterface $client) {
        $this->client = $client;
    }

    public function get(string $ip) : IpGeolocationItem {
        $content = $this->getContentFromRequest($ip);
        return $this->buildGeolocationItem($content);
    }

    protected function getContentFromRequest($ip) : array {
        $response = $this->request($ip);
        $this->checkResponse($response);

        $content = $response->toArray();
        $this->checkContent($content);

        return $content;
    }

    protected function buildGeolocationItem(array $content) : IpGeolocationItem {
        $geo_ip_item = new IpGeolocationItem();
        if (static::COUNTRY_CODE_KEY !== null && isset($content[static::COUNTRY_CODE_KEY])) {
            $geo_ip_item->setCountryCode($content[static::COUNTRY_CODE_KEY]);
        }

        return $geo_ip_item;
    }

    protected function checkResponse(ResponseInterface $response) : void {
        $headers = $response->getHeaders();
        if ($response->getStatusCode() !== 200 || !(isset($headers['content-type'][0]) && str_contains($headers['content-type'][0], 'application/json'))) {
            throw new \RuntimeException(sprintf('communication error with API provider "%s"', static::NAME));
        }
    }

}
