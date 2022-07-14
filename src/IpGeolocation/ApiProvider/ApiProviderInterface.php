<?php

namespace App\IpGeolocation\ApiProvider;

use App\IpGeolocation\IpGeolocationItem;

interface ApiProviderInterface {

    public function get(string $ip) : IpGeolocationItem;

}
