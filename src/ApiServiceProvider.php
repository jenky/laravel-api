<?php

namespace Jenky\LaravelAPI;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\ResponseMixins;
use Jenky\LaravelAPI\Http\Routing\Router;
use Jenky\LaravelAPI\Http\Validator\Domain;
use Jenky\LaravelAPI\Http\Validator\Prefix;
use Jenky\LaravelAPI\Http\VersionParser\Header;
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
        $this->setupConfig();

        $this->app->singleton(Validator::class, function () {
            return $this->createRequestValidator();
        });

        $this->app->singleton(VersionParser::class, function () {
            return $this->createVersionParser();
        });

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
        $this->registerRequestMacros();
        $this->registerResponseMacros();
        $this->registerRouterMacros();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $configPath = __DIR__.'/../config/api.php';
        $this->mergeConfigFrom($configPath, 'api');

        if ($this->app->runningInConsole()) {
            $this->publishes([$configPath => config_path('api.php')], 'config');
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
                return new Prefix($this->config('prefix'));
                break;

            case 'domain':
                return new Domain($this->config('domain'));
                break;

            default:
                throw new RuntimeException('Invalid API scheme configuration.');
                break;
        }
    }

    /**
     * Create a version parser.
     *
     * @throws \RuntimeException
     * @return \Jenky\LaravelAPI\Contracts\Http\VersionParser
     */
    protected function createVersionParser()
    {
        $method = 'create'.Str::studly($this->config('version_scheme')).'VersionParser';

        if (method_exists($this, $method)) {
            $this->{$method}();
        }

        // throw new RuntimeException('Invalid API version scheme configuration.');
    }

    protected function createHeaderVersionParser()
    {
        return new Header;
    }

    /**
     * Register request macros.
     *
     * @return void
     */
    protected function registerRequestMacros()
    {
        $validator = $this->app->make(Validator::class);

        $this->app['request']->macro('isApi', function () use ($validator) {
            static $isApi;

            if (isset($isApi)) {
                return $isApi;
            }

            return $isApi = $validator->validate($this);
        });
    }

    /**
     * Register response macros.
     *
     * @return void
     */
    protected function registerResponseMacros()
    {
        Response::mixin(new ResponseMixins);
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
