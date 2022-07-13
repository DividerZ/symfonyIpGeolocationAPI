<?php

namespace App\IpGeolocation;

class GeolocationIpItem {

    public const FIELD_COUNTRY_CODE = 'country_code';

    private string $countryCode = '';

    public function setCountryCode(string $country_code) : void {
        $this->countryCode = $country_code;
    }

    public function getCountryCode() : string {
        return $this->countryCode;
    }

}