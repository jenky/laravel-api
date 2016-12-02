<?php

namespace Jenky\LaravelAPI\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\Contracts\Http\Validator;

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

    /**
     * Create a new middleware request.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @param  \Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler $exception
     * @return void
     */
    public function __construct(Application $app, ExceptionHandler $exception)
    {
        $this->app = $app;
        $this->exception = $exception;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if ($this->app[Validator::class]->validate($request)) {
                $this->app->singleton(LaravelExceptionHandler::class, function ($app) {
                    return $app[ExceptionHandler::class];
                });
            }
        } catch (Exception $exception) {
            $this->exception->report($exception);

            return $this->exception->render($request, $exception);
        }

        return $next($request);
    }
}
