<?php

namespace Jenky\LaravelAPI\Macros;

class RouteMacros
{
    /**
     * Get the route versions.
     *
     * @return array|null
     */
    public function version()
    {
        return function () {
            return $this->action['versions'] ?? null;
        };
    }
}
