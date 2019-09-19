<?php

namespace Jenky\LaravelAPI;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\Validator\DomainValidator;
use Jenky\LaravelAPI\Http\Validator\PrefixValidator;
use Jenky\LaravelAPI\Http\VersionParser\Header;
use Jenky\LaravelAPI\Http\VersionParser\Uri;
use Jenky\LaravelAPI\Macros\RequestMacros;
use Jenky\LaravelAPI\Macros\ResponseMacros;
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

        $this->app->singleton(Validator::class, function () {
            return $this->createRequestValidator();
        });

        $this->registerVersionParser();

        // $this->app->singleton(Parser::class, function ($app) {
        //     return new AcceptParser($this->config('standards_tree'), $this->config('subtype'), $this->config('version'), 'json');
        // });
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
                __DIR__.'/../config/api.php' => config_path('api.php')
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
     * Create a request validator.
     *
     * @throws \RuntimeException
     * @return \Jenky\LaravelAPI\Contracts\Http\Validator
     */
    protected function createRequestValidator()
    {
        switch ($this->config('uri_scheme')) {
            case 'prefix':
                return new PrefixValidator($this->config('prefix'));
                break;

            case 'domain':
                return new DomainValidator($this->config('domain'));
                break;

            default:
                throw new RuntimeException('Invalid API scheme configuration.');
                break;
        }
    }

    /**
     * Register the package version parser.
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function registerVersionParser()
    {
        $method = 'register'.Str::studly($this->config('version_scheme')).'VersionParser';

        if (method_exists($this, $method)) {
            $this->{$method}();
        }
    }

    /**
     * Register the package header version parser.
     *
     * @return void
     */
    protected function registerHeaderVersionParser()
    {
        $this->app->singleton(VersionParser::class, function () {
            return new Header($this->app['config']);
        });
    }

    /**
     * Register the package URI version parser.
     *
     * @return void
     */
    protected function registerUriVersionParser()
    {
        $this->app->singleton(VersionParser::class, function () {
            return new Uri;
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
     * Register router macros.
     *
     * @return void
     */
    protected function registerRouterMacros()
    {
        $this->app['router']->mixin(new RouterMacros);
    }
}
