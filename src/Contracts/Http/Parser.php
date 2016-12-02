<?php

namespace Jenky\LaravelAPI\Contracts\Http;

use Illuminate\Http\Request;

interface Parser
{
    /**
     * Parse the accept header on the incoming request. If strict is enabled
     * then the accept header must be available and must be a valid match.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  bool $strict
     * @return array
     */
    public function parse(Request $request, $strict = false);
}
