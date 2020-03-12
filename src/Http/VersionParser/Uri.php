<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\Concerns\InteractsWithPath;

class Uri implements VersionParser
{
    use InteractsWithPath;

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
     * Parse the request and get the API version.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    public function parse(Request $request): ?string
    {
        $paths = $this->explode(
            str_replace($this->config->get('api.prefix'), '', $request->getPathInfo())
        );

        return Arr::first($paths);
    }
}
