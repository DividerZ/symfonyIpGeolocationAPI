<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class IpController {

    private ValidatorInterface $validator;
    private HttpClientInterface $client;

    public function __construct(ValidatorInterface $validator, HttpClientInterface $client) {
        $this->validator = $validator;
        $this->client = $client;
    }

    public function countryCodeGet(string $ip) : JsonResponse {
        $violation_list = $this->validator->validate($ip, new Assert\Ip(
            null,
            Assert\Ip::ALL
        ));

        if ($violation_list->count() > 0) {
            return $this->answerFail();
        }

        $data_version = 1.0;
        //$cache = new ApcuAdapter('ipToLocation');
        //$cache = new FilesystemAdapter('ipToLocation');


        $memcached_client = MemcachedAdapter::createConnection('memcached://localhost:11211');
        $cache = new MemcachedAdapter($memcached_client, 'ipToLocation');
        $ip_location_info = $cache->get($ip, function(ItemInterface $item) use ($ip) {
            $item->expiresAfter(30);
            $response = $this->client->request('GET', 'http://ip-api.com/json/'.$ip.'?fields=status,countryCode,query');

            $value = null;
            $headers = $response->getHeaders();
            if ($response->getStatusCode() == 200 && (isset($headers['content-type'][0]) && str_contains($headers['content-type'][0], 'application/json'))) {
                $content = $response->toArray();
                if (isset($content['countryCode'])) {
                    $value = ['countryCode' => $content['countryCode']];
                }
            }
            return $value;
        }, $data_version);

        return null !== $ip_location_info
            ? $this->answerSuccess($ip_location_info)
            : $this->answerFail();
    }

    protected function answerFail() : JsonResponse {
        return new JsonResponse(['status' => 'fail'], 300, []);
    }

    protected function answerSuccess(array $response) : JsonResponse {
        return new JsonResponse(['status' => 'success', 'response' => $response], 200, []);
    }
}