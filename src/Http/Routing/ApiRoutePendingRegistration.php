<?php

namespace Jenky\LaravelAPI\Http\Routing;

use Illuminate\Container\Container;
use Illuminate\Support\Traits\ForwardsCalls;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\VersionParser\Header;

class ApiRoutePendingRegistration
{
    use ForwardsCalls;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The API route version.
     *
     * @var string
     */
    protected $version;

    /**
     * The API registrar.
     *
     * @var \Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar
     */
    protected $registrar;

    /**
     * Create a new API route registration instance.
     *
     * @param  \Jenky\LaravelAPI\Http\Routing\ApiRouteRegistrar $registrar
     * @param  string $version
     * @param  \Illuminate\Container\Container|null
     * @return void
     */
    public function __construct(ApiRouteRegistrar $registrar, $version, Container $container = null)
    {
        $this->container = $container ?: Container::getInstance();
        $this->version = $version;
        $this->registrar = $registrar->attribute('version', $version);
    }

    /**
     * Determine whether route registration should be forwarded to router.
     *
     * @return bool
     */
    protected function shouldForwardCall(): bool
    {
        $versionParser = $this->container[VersionParser::class]->driver();

        if ($versionParser instanceof Header) {
            // Only applied if the header versioning is used.
            // The route that doesn't match request header version
            // won't be registered, thus it can't be found within route list.
            // ? Create a sandbox route registrar
            return $versionParser->parse(
                $this->container['request']
            ) == $this->version;
        }

        return true;
    }

    /**
     * Dynamically handle calls into the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! $this->shouldForwardCall()) {
            return;
        }

        return $this->forwardCallTo($this->registrar, $method, $parameters);
    }
}
