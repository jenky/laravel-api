<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class PrefixVersioningTest extends FeatureTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('api.version_scheme', 'uri');
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
     * Set up routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        Route::prefix('api/v1')
        // Route::prefix('api')
        //     ->api('v1')
            ->group(function () {
                Route::get('/', function () {
                    return $this->getResponseBody('v1');
                });
            });

        Route::prefix('api/v2')
            ->group(function () {
                Route::get('/', function () {
                    return $this->getResponseBody('v2');
                });
            });
    }

    public function test_api_v1_prefix()
    {
        $this->get('api/v1')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v1',
                    'request' => 'v1',
                ],
            ]);
    }

    public function test_api_v2_prefix()
    {
        $this->get('api/v2')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'request' => 'v2',
                ],
            ]);
    }
}
