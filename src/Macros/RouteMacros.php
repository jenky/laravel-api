<?php

namespace Jenky\LaravelAPI\Macros;

class RouteMacros
{
    /**
     * Set the route versions.
     *
     * @return array|null
     */
    public function api()
    {
        return function ($version) {
            return $this->action['version'] = $version;
        };
    }

    /**
     * Get the route versions.
     *
     * @return array|null
     */
    public function version()
    {
        return function () {
            return $this->action['version'] ?? null;
        };
    }
}
