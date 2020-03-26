<?php

namespace Jenky\LaravelAPI\Http\Routing;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteRegistrar;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class Router
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Jenky\LaravelAPI\Contracts\Http\VersionParser
     */
    protected $versionParser;

    /**
     * Create router class.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     * @param  \Illuminate\Config\Repository $config
     * @param  \Jenky\LaravelAPI\Contracts\Http\VersionParser $parser
     * @return void
     */
    public function __construct(Request $request, Registrar $router, Config $config, VersionParser $parser)
    {
        $this->request = $request;
        $this->router = $router;
        $this->config = $config;
        $this->versionParser = $parser;
    }

    /**
     * Create API route group.
     *
     * @param  string $version
     * @param  string[] ...$args
     * @return void|\Illuminate\Contracts\Routing\Registrar
     */
    public function register($version, ...$args)
    {
        [$attributes, $callback] = $this->parseRouteParameters($version, $args);

        if (! is_array($attributes)) {
            return;
        }

        if ($callback) {
            return $this->router->group($attributes, $callback);
        }

        $router = new RouteRegistrar($this->router);

        foreach ($attributes as $key => $value) {
            $router->attribute($key, $value);
        }

        return $router;
    }

    /**
     * Parse the parameters to pass to router.
     *
     * @param  string $version
     * @param  array $args
     * @return array
     */
    protected function parseRouteParameters($version, array $args)
    {
        $attributes = $this->mergeRouteAttributes($version, []);
        $callback = null;

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $attributes = $this->mergeRouteAttributes($version, $arg);
            }

            if ($arg instanceof Closure) {
                $callback = $arg;
                break;
            }
        }

        return [$attributes, $callback];
    }

    /**
     * Merge API version to route attributes.
     *
     * @param  string $version
     * @param  array $attributes
     * @return array|null
     */
    protected function mergeRouteAttributes($version, array $attributes)
    {
        switch ($this->config->get('api.uri_scheme')) {
            case 'prefix':
                $attributes['prefix'] = $this->config->get('api.prefix');
                break;

            case 'domain':
                $attributes['domain'] = $this->injectSubdomain(
                    $this->config->get('api.domain'), $this->config->get('app.url')
                );
                break;
        }

        switch ($this->config->get('api.version_scheme')) {
            case 'prefix':
                if (! empty($attributes['prefix'])) {
                    $attributes['prefix'] = trim($attributes['prefix'], '/').'/'.$version;
                } else {
                    $attributes['prefix'] = $version;
                }
                break;

            case 'header':
                if ($version != $this->getVersionFromRequest()) {
                    return;
                }
                break;
        }

        return $attributes;
    }

    /**
     * Get version from request header.
     *
     * @return string
     */
    protected function getVersionFromRequest()
    {
        return $this->versionParser->parse($this->request) ?: $this->config->get('api.version');
    }

    /**
     * Inject subdomain into URL.
     *
     * @param  string $url
     * @param  string $subdomain
     * @return string
     */
    protected function injectSubdomain(string $subdomain, string $url)
    {
        $parts = explode('://',$url);

        return $parts[0].'://'.$subdomain.'.'.$parts[1];
    }
}
