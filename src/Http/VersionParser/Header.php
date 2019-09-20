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
     * @return string|null
     */
    public function parse(Request $request): ?string
    {
        return VersionParser::DEFAULT;
    }
}
