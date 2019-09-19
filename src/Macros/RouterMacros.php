<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Http\Middleware\ApiVersionMiddleware;

class RouterMacros
{
    public function api()
    {
        return function (...$versions) {
            return $this->middleware(
                ApiVersionMiddleware::class.':'.implode(',', $versions)
            );
        };
    }
}
