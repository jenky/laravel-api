<?php

namespace Jenky\LaravelAPI;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Jenky\LaravelAPI\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\Exception\Handler;
use Jenky\LaravelAPI\Http\Middleware\Request;
use Jenky\LaravelAPI\Http\Validator\Prefix;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;
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
        $this->app->singleton(Domain::class, function ($app) {
            return new Domain('api');
        });

        $this->app->singleton(Prefix::class, function ($app) {
            return new Prefix('api');
        });

        $this->app->singleton(ExceptionHandler::class, function ($app) {
            return new Handler($this->app);
        });

        $this->registerResponseMacros();
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

        Response::macro('transform', function ($data, TransformerAbstract $transformer, callable $callback = null) use ($response) {
            return $response(fractal($data, $transformer), $callback);
        });

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
    }
}
