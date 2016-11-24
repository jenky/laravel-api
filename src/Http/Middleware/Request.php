<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\Http\RequestValidator;

class Request
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $exception;

    public function __construct(Application $app, ExceptionHandler $exception)
    {
        $this->app = $app;
        $this->exception = $exception;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $validator = $this->app->make(RequestValidator::class);

            if ($validator->validateRequest($request)) {
                $this->app->singleton(LaravelExceptionHandler::class, function ($app) {
                    return $app[ExceptionHandler::class];
                });
            }
        } catch (Exception $exception) {
            $this->exception->report($exception);

            return $this->exception->handle($exception);
        }

        return $next($request);
    }
}
