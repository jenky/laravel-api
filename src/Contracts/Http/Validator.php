<?php

namespace Jenky\LaravelAPI\Contracts\Http;

use Illuminate\Http\Request;

interface Validator
{
    /**
     * Validate a request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function matches(Request $request): bool;
}
