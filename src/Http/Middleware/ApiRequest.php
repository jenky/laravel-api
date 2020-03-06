<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if ($this->isApiRequest($request) && ! $request->headers->has('Accept')) {
            $request->headers->set('Accept', 'application/json');
        }

        dd($request->headers, $request->expectsJson());

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
}
