<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class HeaderVersioningTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('api.version_scheme', 'header');

        $this->loadRoutes();
    }

    protected function loadRoutes()
    {
        Route::prefix('api')
            ->group(function () {
                Route::prefix('v1')
                    ->get('/', function () {
                        return $this->getResponseBody('v1');
                    });

                Route::prefix('v2')
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

    /**
     * Todo: Fix all below test cases since request headers are dropped.
     */
    public function test_api_invalid_version_header()
    {
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
        $this->getJson('/api', ['Accept' => 'application/x.laravel.v2+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                ],
            ]);
    }
}
