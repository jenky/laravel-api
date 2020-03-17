<?php

namespace Jenky\LaravelAPI\Test;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Jenky\LaravelAPI\ApiServiceProvider;
use Jenky\LaravelAPI\Test\Fixtures\ExceptionHandler as Handler;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ApiServiceProvider::class,
        ];
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('database.default', 'testbench');

        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $config->set('app.debug', true);
    }

    /**
     * Get the test response for testing.
     *
     * @param  string|null $version
     * @return array
     */
    protected function getResponseBody($version = null)
    {
        return [
            'version' => [
                'set' => $version,
                'request' => request()->version(),
            ],
        ];
    }
}
