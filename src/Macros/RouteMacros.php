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
        return function (...$versions) {
            return $this->action['versions'] = array_unique(array_merge(
                $versions, ($this->action['versions'] ?? [])
            ));
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
            return $this->action['versions'] ?? null;
        };
    }
}
