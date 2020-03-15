<?php

namespace Jenky\LaravelAPI\Http\Routing;

use Illuminate\Contracts\Foundation\Application;
use Jenky\LaravelAPI\Contracts\Http\Validator;
use Jenky\LaravelAPI\Http\Validator\DomainValidator;
use Jenky\LaravelAPI\Http\Validator\PrefixValidator;

class RouteRegistrarResolver
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function resolveAttribute()
    {
        if ($this->app[Validator::class] instanceof PrefixValidator) {
            return 'prefix';
        }

        if ($this->app[Validator::class] instanceof DomainValidator) {
            return 'domain';
        }

        return null;
    }

    public function __invoke()
    {
        $self = $this;

        return function ($api, array $attributes = [], $routes = null) use ($self) {
            $attribute = $self->resolveAttribute();

            if (count(func_get_args()) > 1) {
                $attributes[$attribute] = $api;

                return $this->group($attributes, $routes);
            }

            dd('aaa');
            return $this->{$attribute}($api);
        };
    }
}
