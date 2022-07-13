<?php

namespace App\Controller;

use App\IpGeolocation\IpGeolocationService;
use Symfony\Component\HttpFoundation\JsonResponse;

class IpController {

    private IpGeolocationService $ipGeolocationService;

    public function __construct(IpGeolocationService $geolocation_service) {
        $this->ipGeolocationService = $geolocation_service;
    }

    public function countryCodeGet(string $ip) : JsonResponse {
        try{
            $geolocation_item = $this->ipGeolocationService->get($ip);
        } catch(\Exception $e) {
            error_log((string) $e);
            return $this->answerFail();
        }

        return $this->answerSuccess([
            'countryCode' => $geolocation_item->getCountryCode()
        ]);
    }

    protected function answerFail() : JsonResponse {
        return new JsonResponse(['success' => false], 400, []);
    }

    protected function answerSuccess(array $response) : JsonResponse {
        return new JsonResponse(['success' => true, 'response' => $response], 200, []);
    }
}