<?php

use App\Providers\AppServiceProvider;
use App\Providers\BaseColorGuestServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    BaseColorGuestServiceProvider::class,
];
