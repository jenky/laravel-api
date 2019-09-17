<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class Uri implements VersionParser
{
    /**
     * Parse the request an get the API version.
     *
     * @return string
     */
    public function version(Request $request): string
    {
        return VersionParser::DEFAULT;
    }
}
