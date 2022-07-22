<?php

namespace App\IpGeolocation;

class IpGeolocationItem {

    private string $countryCode = '';

    public function setCountryCode(string $country_code) : void {
        $this->countryCode = $country_code;
    }

    public function getCountryCode() : string {
        return $this->countryCode;
    }

}