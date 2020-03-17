<?php

namespace Jenky\LaravelAPI;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\Middleware\ApiRequest;
use Jenky\LaravelAPI\Http\Routing\Router;
use Jenky\LaravelAPI\Http\Validator\ValidatorManager;
use Jenky\LaravelAPI\Http\VersionParser\VersionParserManager;
use Jenky\LaravelAPI\Macros\ResponseMacros;

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
        $this->app[Kernel::class]->prependMiddleware(ApiRequest::class);

        $this->registerPublishing();

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
        $parser = $this->app->make(VersionParser::class);

        $this->app['request']->macro('version', function () use ($parser) {
            static $version;

            if (isset($version)) {
                return $version;
            }

            return $version = $parser->parse($this);
        });
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
     * Register router macros.
     *
     * @return void
     */
    protected function registerRouterMacros()
    {
        $router = $this->app->make(Router::class);

        $this->app['router']->macro('api', function ($version, ...$args) use ($router) {
            return $router->register($version, ...$args);
        });
    }
}
