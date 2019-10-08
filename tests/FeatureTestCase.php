<?php

namespace Jenky\LaravelAPI\Test;

use Jenky\LaravelAPI\ApiServiceProvider;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function getEnvironmentSetUp($app)
    {
        require 'routes.php';
    }

    /**
     * Get base path.
     *
     * @return string
     */
    // protected function getBasePath()
    // {
    //     return __DIR__.'/../../';
    // }

    protected function getPackageProviders($app)
    {
        return [
            ApiServiceProvider::class,
        ];
    }
}
