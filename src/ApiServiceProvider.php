<?php

namespace Jenky\LaravelAPI;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\Exception\Handler;
use Jenky\LaravelAPI\Http\Middleware\Request;
use Jenky\LaravelAPI\Http\Validator\Prefix;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app[Kernel::class]->prependMiddleware(Request::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Domain::class, function ($app) {
            return new Domain(null);
        });

        $this->app->singleton(Prefix::class, function ($app) {
            return new Prefix('api');
        });

        $this->app->singleton(ExceptionHandler::class, function ($app) {
            return new Handler($this->app);
        });
    }
}
