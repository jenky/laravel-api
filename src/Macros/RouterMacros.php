<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar;

class RouterMacros
{
    /**
     * Assign version to route.
     *
     * @return \Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar
     */
    public function api()
    {
        return function ($version) {
            return (new ApiRouteRegistrar($this))->attribute('version', $version);
        };
    }
}
