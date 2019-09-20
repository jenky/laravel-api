<?php

namespace Jenky\LaravelAPI\Macros;

use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class RequestMacros
{
    /**
     * Determine whether current URI is an API request.
     *
     * @return bool
     */
    public function isApi()
    {
        return function () {
            static $isApi;

            if (isset($isApi)) {
                return $isApi;
            }

            return $isApi = resolve(Validator::class)->matches($this);
        };
    }

    /**
     * Get the API version of the request.
     *
     * @return string|null
     */
    public function version()
    {
        return function ($strict = false) {
            if ($strict && ! $this->isApi()) {
                return;
            }

            static $version;

            if (isset($version)) {
                return $version;
            }

            return $version = resolve(VersionParser::class)->parse($this);
        };
    }
}
