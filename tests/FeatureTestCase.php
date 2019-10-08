<?php

namespace Jenky\LaravelAPI\Test;

use Jenky\LaravelAPI\ApiServiceProvider;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ApiServiceProvider::class,
        ];
    }
}
