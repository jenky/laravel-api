<?php

namespace Jenky\LaravelAPI\Http\Validator;

use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\Validator;

class Domain implements Validator
{
    /**
     * Validate that the request domain matches the configured domain.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function validate(Request $request)
    {
        return false;
        return ! is_null($this->domain) && $request->getHost() === $this->getStrippedDomain();
    }
}
