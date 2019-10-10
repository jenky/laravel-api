<?php

namespace Jenky\LaravelAPI\Http\VersionParser;

use Illuminate\Support\Manager;

class VersionParserManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']->get('api.version_scheme');
    }

    /**
     * Get the header version parser.
     *
     * @return \Jenky\LaravelAPI\Http\VersionParser\Header
     */
    protected function createHeaderDriver()
    {
        return new Header($this->app['config']);
    }

    /**
     * Get the uri version parser.
     *
     * @return \Jenky\LaravelAPI\Http\VersionParser\Uri
     */
    protected function createUriDriver()
    {
        return new Uri;
    }
}
