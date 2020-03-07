<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class HeaderVersioningTest extends FeatureTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('api.version_scheme', 'header');
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadRoutes();
    }

    /**
     * Set up routes
     *
     * @return void
     */
    protected function loadRoutes()
    {
        Route::prefix('api')
            ->group(function () {
                Route::api('v1')
                    ->get('/', function () {
                        return $this->getResponseBody('v1');
                    });

                Route::api('v2')
                    ->get('/', function () {
                        return $this->getResponseBody('v2');
                    });
            });
    }

    public function test_api_default_version_header()
    {
        $config = $this->app->get('config');

        $this->getJson('/api')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => $config->get('api.version'),
                ],
            ]);
    }

    public function test_api_invalid_version_header()
    {
        // Todo: Fix all below test cases since request headers are dropped.
        $this->getJson('/api', ['Accept' => 'application/x.laravel.v3+json'])
            ->assertNotFound();
    }

    public function test_api_v1_header()
    {
        $this->getJson('/api', ['Accept' => 'application/x.laravel.v1+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v1',
                ],
            ]);
    }

    public function test_api_v2_header()
    {
        // Todo: Fix all below test cases since request headers are dropped.
        $this->getJson('/api', ['Accept' => 'application/x.laravel.v2+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                ],
            ]);
    }
}
