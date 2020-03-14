<?php

namespace Jenky\LaravelAPI;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\Middleware\ApiRequest;
use Jenky\LaravelAPI\Http\Validator\ValidatorManager;
use Jenky\LaravelAPI\Http\VersionParser\VersionParserManager;
use Jenky\LaravelAPI\Macros\RequestMacros;
use Jenky\LaravelAPI\Macros\ResponseMacros;
use Jenky\LaravelAPI\Macros\RouteMacros;
use Jenky\LaravelAPI\Macros\RouterMacros;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/api.php', 'api');

        $this->registerRequestValidator();

        $this->registerVersionParser();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();

        $this->app[Kernel::class]->prependMiddleware(ApiRequest::class);

        $this->registerRequestMacros();

        $this->registerResponseMacros();

        $this->registerRouterMacros();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/api.php' => config_path('api.php'),
            ], 'config');
        }
    }

    /**
     * Get API config value.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return $this->app['config']->get('api.'.$key, $default);
    }

    /**
     * Register the package request validator.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function registerRequestValidator()
    {
        $this->app->singleton(ValidatorManager::class, function ($app) {
            return new ValidatorManager($app);
        });

        $this->app->singleton(Validator::class, function ($app) {
            return $app->make(ValidatorManager::class)->driver();
        });
    }

    /**
     * Register the package version parser.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function registerVersionParser()
    {
        $this->app->singleton(VersionParserManager::class, function ($app) {
            return new VersionParserManager($app);
        });

        $this->app->singleton(VersionParser::class, function ($app) {
            return $app->make(VersionParserManager::class)->driver();
        });
    }

    /**
     * Register request macros.
     *
     * @return void
     */
    protected function registerRequestMacros()
    {
        $this->app['request']->mixin(new RequestMacros);
    }

    /**
     * Register response macros.
     *
     * @return void
     */
    protected function registerResponseMacros()
    {
        Response::mixin(new ResponseMacros);
    }

    /**
     * Register router and route macros.
     *
     * @return void
     */
    protected function registerRouterMacros()
    {
        $this->app['router']->mixin(new RouterMacros);

        Route::mixin(new RouteMacros);
    }
}
