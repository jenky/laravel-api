<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\VersionParser\Uri;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class PrefixVersioningTest extends FeatureTestCase
{
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

    public function test_version_parser_is_uri()
    {
        $this->assertInstanceOf(Uri::class, $this->app[VersionParser::class]);
    }

    public function test_api_v1_prefix()
    {
        $this->loadRoutes();

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
        $this->loadRoutes();

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
