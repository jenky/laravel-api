<?php

namespace Jenky\LaravelAPI;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\Contracts\Http\Parser;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Http\AcceptParser;
use Jenky\LaravelAPI\Http\Middleware\Request;
use Jenky\LaravelAPI\Http\Router;
use Jenky\LaravelAPI\Http\Validator\Domain;
use Jenky\LaravelAPI\Http\Validator\Prefix;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;
use RuntimeException;
use Spatie\Fractal\Fractal;
use Spatie\Fractal\FractalServiceProvider;

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
        $this->app->register(FractalServiceProvider::class);
    }

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

        $this->app->singleton(ExceptionHandler::class, function ($app) {
            $handler = $this->config('handlers.exception');

            return new $handler($app);
        });

        $this->app->singleton(Parser::class, function ($app) {
            return new AcceptParser($this->config('standardsTree'), $this->config('subtype'), $this->config('version'), 'json');
        });

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
        $this->publishes([$configPath => config_path('api.php')], 'config');
        $this->mergeConfigFrom($configPath, 'api');
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
     * Register response macros.
     *
     * @return void
     */
    protected function registerResponseMacros()
    {
        $response = function (Fractal $fractal, callable $callback = null) {
            if ($callback) {
                // $fractal = $callback($fractal);
                return $callback($fractal);
            }

            return $fractal->toJson();
        };

        Response::macro('item', function ($data, TransformerAbstract $transformer, callable $callback = null) use ($response) {
            return $response(fractal()->item($data, $transformer), $callback);
        });

        Response::macro('collection', function ($data, TransformerAbstract $transformer, callable $callback = null) use ($response) {
            return $response(fractal()->collection($data, $transformer), $callback);
        });

        Response::macro('paginator', function (LengthAwarePaginator $data, TransformerAbstract $transformer, callable $callback = null) use ($response) {
            $fractal = fractal()->collection($data->getCollection(), $transformer)
                ->paginateWith(new IlluminatePaginatorAdapter($data));

            return $response($fractal, $callback);
        });

        Response::macro('transform', function ($data, TransformerAbstract $transformer, callable $callback = null) use ($response) {
            if ($data instanceof LengthAwarePaginator) {
                return $this->paginator($data, $transformer, $callback);
            }

            return $response(fractal($data, $transformer), $callback);
        });
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
