<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenky\LaravelAPI\Contracts\Http\Validator;

class ApiRequest
{
    /**
     * The HTTP validator instance.
     *
     * @var \Jenky\LaravelAPI\Contracts\Http\Validator
     */
    protected $validator;

    /**
     * Create a new middleware instance.
     *
     * @param  \Jenky\LaravelAPI\Contracts\Http\Validator $validator
     * @return void
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        if ($this->isApiRequest($request) && ! $this->wantsJson($request)) {
            // Set default Accept header if not available to force the request
            // to return JSON response
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }

    /**
     * Determine whether current request is API request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function isApiRequest(Request $request): bool
    {
        return $this->validator->matches($request);
    }

    /**
     * Determine if the current request is asking for JSON.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function wantsJson(Request $request): bool
    {
        // We can't use $request->wantsJson() because it will
        // cache the Accept header for subsequent check.
        return Str::contains($request->header('Accept'), ['/json', '+json']);
    }
}
