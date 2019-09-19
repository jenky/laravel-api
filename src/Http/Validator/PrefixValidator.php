<?php

namespace Jenky\LaravelAPI\Http\Validator;

use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\Validator;

class PrefixValidator implements Validator
{
    /**
     * API prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new prefix validator instance.
     *
     * @param  string $prefix
     * @return void
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Validate the request has a prefix and if it matches the configured
     * API prefix.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function validate(Request $request): bool
    {
        $prefix = $this->filterAndExplode($this->prefix);

        $path = $this->filterAndExplode($request->getPathInfo());

        return ! is_null($this->prefix) && $prefix == array_slice($path, 0, count($prefix));
    }

    /**
     * Explode array on slash and remove empty values.
     *
     * @param  array $array
     * @return array
     */
    protected function filterAndExplode($array)
    {
        return array_filter(explode('/', $array));
    }
}
