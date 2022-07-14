<?php

namespace App\Controller;

use App\IpGeolocation\IpGeolocationService;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IpController {

    private IpGeolocationService $ipGeolocationService;

    public function __construct(IpGeolocationService $geolocation_service) {
        $this->ipGeolocationService = $geolocation_service;
    }

    public function countryCodeGet(Request $request) : JsonResponse {
        $ip = $request->get('ip');
        if (empty($ip)) {
            return $this->answerFail(400);
        }

        try {
            $geolocation_item = $this->ipGeolocationService->get($ip);
        } catch (InvalidArgumentException $e) {
            error_log((string) $e);
            return $this->answerFail(400);
        } catch(\Exception $e) {
            error_log((string) $e);
            return $this->answerFail(500);
        }

        return $this->answerSuccess([
            'countryCode' => $geolocation_item->getCountryCode()
        ]);
    }

    protected function answerFail(int $http_status) : JsonResponse {
        return new JsonResponse(['success' => false], $http_status);
    }

    protected function answerSuccess(array $response) : JsonResponse {
        return new JsonResponse(['success' => true, 'response' => $response], 200);
    }
}