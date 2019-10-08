<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;

class Header implements VersionParser
{
    /**
     * The config repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create new header version parser.
     *
     * @param  \Illuminate\Contracts\Config\Repository $config
     * @return void
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Parse the request an get the API version.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    public function parse(Request $request): ?string
    {
        $parsed = $this->getAcceptParser($request)
            ->parse($request, $this->config->get('api.strict'));

        return $parsed['version'] ?? null;
    }

    /**
     * Get the Accept header parser.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Jenky\LaravelAPI\Http\VersionParser\AcceptParser
     */
    protected function getAcceptParser(Request $request): AcceptParser
    {
        $route = $request->route();
        $version = $route ? $route->version() : $this->config->get('api.version');

        return new AcceptParser(
            $this->config->get('api.standards_tree'),
            $this->config->get('api.subtype'),
            $version,
            $this->config->get('api.format', 'json')
        );
    }
}
