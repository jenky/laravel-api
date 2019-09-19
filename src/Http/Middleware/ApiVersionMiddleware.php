<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;

class ApiVersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$versions
     * @return mixed
     */
    public function handle($request, Closure $next, ...$versions)
    {
        $request->route()->action['versions'] = $versions;

        return $next($request);
    }
}
