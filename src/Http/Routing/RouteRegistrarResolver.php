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

    /**
     * The API request validator instance.
     *
     * @var \Jenky\LaravelAPI\Contracts\Http\Validator
     */
    protected $validator;

    /**
     * Create new route registrar resolver instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->validator = $app->make(Validator::class);
    }

    /**
     * Resolve route attribute based on API config.
     *
     * @return string|null
     */
    public function resolveAttribute(): ?string
    {
        if ($this->validator instanceof PrefixValidator) {
            return 'prefix';
        }

        if ($this->validator instanceof DomainValidator) {
            return 'domain';
        }

        return null;
    }

    /**
     * Parse the parameters for route group.
     *
     * @param  string $version
     * @param  array $parameters
     * @return array
     */
    public function parseGroupParameters(string $version, array $parameters): array
    {
        if (is_callable($parameters[0]) || is_string($parameters[0])) {
            return [[$this->resolveAttribute() => $version], $parameters[0]];
        }

        $parameters[0][$this->resolveAttribute()] = $version;

        return $parameters;
    }

    /**
     * Handle the route api macro.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return \Illuminate\Routing\Router|\Illuminate\Routing\RouteRegistrar
     */
    public static function macro(Application $app)
    {
        $self = new static($app);

        return function ($version, ...$parameters) use ($self) {
            /** @var \Illuminate\Routing\Router $this */
            if (! empty($parameters)) {
                return $this->group(
                    ...$self->parseGroupParameters($version, $parameters)
                );
            }

            if ($this->container && $this->container->bound(ApiRouteRegistrar::class)) {
                $registrar = $this->container->make(ApiRouteRegistrar::class);
            } else {
                $registrar = new ApiRouteRegistrar($this);
            }

            if ($attribute = $self->resolveAttribute()) {
                $registrar->attribute($attribute, $version);
            }

            return new ApiRoutePendingRegistration(
                $registrar, $version, $this->container
            );
            // return $this->__call($self->resolveAttribute(), [$version]);
        };
    }
}
