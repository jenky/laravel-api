<?php

namespace Jenky\LaravelAPI\Contracts\Http;

use Illuminate\Http\Request;

interface VersionParser
{
    const DEFAULT = '1.0';

    /**
     * Parse the request an get the API version.
     *
     * @return string|null
     */
    public function parse(Request $request): ?string;
}
