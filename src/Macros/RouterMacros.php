<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Http\Middleware\ApiVersionMiddleware;

class RouterMacros
{
    /**
     * Add the API version middleware to the route.
     *
     * @return \Illuminate\Routing\RouteRegistrar
     */
    public function api()
    {
        return function (...$versions) {
            return $this->middleware(
                ApiVersionMiddleware::class.':'.implode(',', $versions)
            );
        };
    }
}
