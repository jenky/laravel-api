<?php

namespace Jenky\LaravelAPI;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Http\Parser;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Http\AcceptParser;
use Jenky\LaravelAPI\Http\Middleware\Request;
use Jenky\LaravelAPI\Http\Response as ApiResponse;
use Jenky\LaravelAPI\Http\Routing\Router;
use Jenky\LaravelAPI\Http\Validator\Domain;
use Jenky\LaravelAPI\Http\Validator\Prefix;
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

        $this->app->singleton(Parser::class, function ($app) {
            return new AcceptParser($this->config('standardsTree'), $this->config('subtype'), $this->config('version'), 'json');
        });

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
                throw new RuntimeException('Missing API scheme configuaration.');
                break;
        }
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
            return $validator->validate($this);
        });
    }

    /**
     * Register response macros.
     *
     * @return void
     */
    protected function registerResponseMacros()
    {
        Response::macro('api', function () {
            return new ApiResponse;
        });

        $methods = [
            'created', 'accepted', 'noContent',
            'error', 'fractal',
        ];

        foreach ($methods as $method) {
            if (! Response::hasMacro($method)) {
                Response::macro($method, function () use ($method) {
                    return call_user_func_array([$this->api(), $method], func_get_args());
                });
            }
        }
    }

    /**
     * Register router macros.
     *
     * @return void
     */
    protected function registerRouterMacros()
    {
        $router = $this->app->make(Router::class);

        $this->app['router']->macro('api', function ($version, $first, $second = null) use ($router) {
            return $router->create($version, $first, $second);
        });
    }
}
