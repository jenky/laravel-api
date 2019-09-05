<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class Header implements VersionParser
{
    /**
     * Parse the request an get the API version.
     *
     * @return string
     */
    public function version(Request $request): string
    {
        //
    }
}
