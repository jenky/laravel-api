<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class RequestMacros
{
    /**
     * Get the API version of the request.
     *
     * @return string|null
     */
    public function version()
    {
        return function () {
            static $version;

            if (isset($version)) {
                return $version;
            }

            return $version = resolve(VersionParser::class)->parse($this);
        };
    }
}
