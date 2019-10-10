<?php

namespace Jenky\LaravelAPI;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\Validator\DomainValidator;
use Jenky\LaravelAPI\Http\Validator\PrefixValidator;
use Jenky\LaravelAPI\Http\Validator\ValidatorManager;
use Jenky\LaravelAPI\Http\VersionParser\Header;
use Jenky\LaravelAPI\Http\VersionParser\Uri;
use Jenky\LaravelAPI\Http\VersionParser\VersionParserManager;
use Jenky\LaravelAPI\Macros\RequestMacros;
use Jenky\LaravelAPI\Macros\ResponseMacros;
use Jenky\LaravelAPI\Macros\RouteMacros;
use Jenky\LaravelAPI\Macros\RouterMacros;
use RuntimeException;

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
        // TODO: clean up
        // $method = 'register'.Str::studly($this->config('uri_scheme')).'Validator';

        // if (method_exists($this, $method)) {
        //     $this->{$method}();
        // } else {
        //     throw new RuntimeException('Invalid API scheme configuration.');
        // }
        $this->app->singleton(Validator::class, function ($app) {
            return new ValidatorManager($app);
        });
    }

    /**
     * Bind the request validator implementation to the application container.
     *
     * @param  \Jenky\LaravelAPI\Contracts\Http\Validator $validator
     * @return void
     */
    protected function bindRequestValidatorToContainer(Validator $validator)
    {
        // TODO: removed
        $this->app->singleton(Validator::class, function () use ($validator) {
            return $validator;
        });
    }

    /**
     * Register the package prefix validator.
     *
     * @return void
     */
    protected function registerPrefixValidator()
    {
        // TODO: removed
        $this->bindRequestValidatorToContainer(
            new PrefixValidator($this->config('prefix'))
        );
    }

    /**
     * Register the package domain validator.
     *
     * @return void
     */
    protected function registerDomainValidator()
    {
        // TODO: removed
        $this->bindRequestValidatorToContainer(
            new DomainValidator($this->config('domain'))
        );
    }

    /**
     * Register the package version parser.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function registerVersionParser()
    {
        // TODO: clean up
        // $method = 'register'.Str::studly($this->config('version_scheme')).'VersionParser';

        // if (method_exists($this, $method)) {
        //     $this->{$method}();
        // } else {
        //     throw new RuntimeException('Invalid version scheme configuration.');
        // }
        $this->app->singleton(VersionParser::class, function ($app) {
            return new VersionParserManager($app);
        });
    }

    /**
     * Bind the version parser implementation to the application container.
     *
     * @param  \Jenky\LaravelAPI\Contracts\Http\VersionParser $parser
     * @return void
     */
    protected function bindVersionParserToContainer(VersionParser $parser)
    {
        // TODO: removed
        $this->app->singleton(VersionParser::class, function () use ($parser) {
            return $parser;
        });
    }

    /**
     * Register the package header version parser.
     *
     * @return void
     */
    protected function registerHeaderVersionParser()
    {
        // TODO: removed
        $this->bindVersionParserToContainer(
            new Header($this->app['config'])
        );
    }

    /**
     * Register the package URI version parser.
     *
     * @return void
     */
    protected function registerUriVersionParser()
    {
        // TODO: removed

        $this->bindVersionParserToContainer(new Uri);
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
