<?php

namespace Jenky\LaravelAPI\Contracts\Http;

use Illuminate\Http\Request;

interface VersionParser
{
    /**
     * Parse the request an get the API version.
     *
     * @return string
     */
    public function version(Request $request): string;
}
