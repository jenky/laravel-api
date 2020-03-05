<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class PrefixVersioningTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('api.version_scheme', 'uri');

        $this->loadRoutes();
    }

    protected function loadRoutes()
    {
        Route::api('v1')
            ->prefix('api/v1')
            ->group(function () {
                Route::get('/', function () {
                    return $this->getResponseBody('v1');
                });
            });

        Route::api('v2')
            ->prefix('api/v2')
            ->group(function () {
                Route::get('/', function () {
                    return $this->getResponseBody('v2');
                });
            });
    }

    public function test_api_v1_prefix()
    {
        $this->getJson('/api/v1')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v1',
                    'route' => 'v1',
                ],
            ]);
    }

    public function test_api_v2_prefix()
    {
        $this->getJson('/api/v2')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'route' => 'v2',
                ],
            ]);
    }
}
