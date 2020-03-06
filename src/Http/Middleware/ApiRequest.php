<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;
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
        if ($this->validator->matches($request) && ! $request->wantsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
