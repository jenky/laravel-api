<?php

namespace Jenky\LaravelAPI\Contracts\Http;

use Illuminate\Http\Request;

interface VersionParser
{
    /**
     * Parse the request an get the API version.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    public function parse(Request $request): ?string;
}
