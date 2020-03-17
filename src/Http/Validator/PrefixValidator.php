<?php

namespace Jenky\LaravelAPI\Http\Validator;

use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Http\Concerns\InteractsWithPath;

class PrefixValidator implements Validator
{
    use InteractsWithPath;

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
    public function matches(Request $request): bool
    {
        $prefix = $this->explode($this->prefix);

        $paths = $this->explode($request->getPathInfo());

        return ! is_null($this->prefix) && $prefix == array_slice($paths, 0, count($prefix));
    }
}
