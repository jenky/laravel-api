<?php

namespace Jenky\LaravelAPI\Macros;

use Composer\Semver\Semver;
use Jenky\LaravelAPI\Contracts\Http\Validator;

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

            return $isApi = resolve(Validator::class)->validate($this);
        };
    }

    public function version()
    {
        return function ($constraints = null) {
            $route = $this->route();

            if (! $route) {
                return;
            }

            $version = $route->action['versions'] ?? [];

            return $constraints ? ! empty(Semver::satisfiedBy($version, $constraints)) : $version;
        };
    }
}
