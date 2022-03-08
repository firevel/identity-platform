<?php

namespace Firevel\IdentityPlatform\Facades;

use Firevel\IdentityPlatform\Services\IdentityPlatformService;
use Illuminate\Support\Facades\Facade;

class IdentityPlatform extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return IdentityPlatformService::class;
    }
}