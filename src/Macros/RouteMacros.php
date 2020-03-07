<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Http\Routing\ApiRoutePendingRegistration;
use Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar;

class RouteMacros
{
    /**
     * Assign version to route.
     *
     * @return \Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar
     */
    public function api()
    {
        return function ($version) {
            return new ApiRoutePendingRegistration(
                $this->container && $this->container->bound(ApiRouteRegistrar::class)
                    ? $this->container->make(ApiRouteRegistrar::class)
                    : new ApiRouteRegistrar($this),
                $version,
                $this->container
            );
        };
    }
}
