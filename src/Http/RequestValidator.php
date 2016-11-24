<?php

namespace Jenky\LaravelAPI\Http;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Http\Validator\Domain;
use Jenky\LaravelAPI\Http\Validator\Prefix;

class RequestValidator
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Array of request validators.
     *
     * @var array
     */
    protected $validators = [
        Domain::class,
        Prefix::class,
    ];

    /**
     * Create a new request validator instance.
     *
     * @param \Illuminate\Container\Container $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Validate a request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function validateRequest(Request $request)
    {
        $passed = false;

        foreach ($this->validators as $validator) {
            $validator = $this->container->make($validator);

            if ($validator instanceof Validator && $validator->validate($request)) {
                $passed = true;
            }
        }

        // The accept validator will always be run once any of the previous validators have
        // been run. This ensures that we only run the accept validator once we know we
        // have a request that is targeting the API.
        // if ($passed) {
        //     $this->container->make(Accept::class)->validate($request);
        // }

        return $passed;
    }
}
