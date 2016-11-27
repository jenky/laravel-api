<?php

namespace Jenky\LaravelAPI\Http;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\Registrar;
use InvalidArgumentException;

class Router
{
    /**
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create router class.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     * @param  \Illuminate\Config\Repository $config
     * @return void
     */
    public function __construct(Registrar $router, Config $config)
    {
        $this->router = $router;
        $this->config = $config;
    }

    /**
     * Create API route group.
     *
     * @param  string $version
     * @param  array|\Closure $first
     * @param  null|\Closure $second
     * @return void
     */
    public function create($version, $first, $second = null)
    {
        list($attributes, $callback) = $this->parseRouteParameters($version, $first, $second);

        return $this->router->group($attributes, $callback);
    }

     /**
     * Parse the parameters to pass to router.
     *
     * @param  string $version
     * @param  array|\Closure $first
     * @param  null|\Closure $second
     * @return array
     */
    protected function parseRouteParameters($version, $first, $second = null)
    {
        if ($first instanceof Closure) {
            $attributes = $this->mergeRouteAttributes($version, []);
            $callback = $first;
        } elseif (is_array($first) && ($second instanceof Closure)) {
            $attributes = $this->mergeRouteAttributes($version, $first);
            $callback = $second;
        } else {
            throw new InvalidArgumentException('The parameters are invalid.');
        }

        return [$attributes, $callback];
    }

    /**
     * Merge API version to route attributes.
     *
     * @param  string $version
     * @param  array $attributes
     * @return array
     */
    protected function mergeRouteAttributes($version, array $attributes)
    {
        switch ($this->config->get('api.scheme')) {
            case 'prefix':
                $attributes['prefix'] = $version;
                break;

            case 'domain':
                $attributes['domain'] = $this->config->get('api.domain');
                $attributes['prefix'] = $version;
                break;

            default:
                break;
        }

        return $attributes;
    }
}
