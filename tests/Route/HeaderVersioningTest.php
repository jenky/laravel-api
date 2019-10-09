<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Contracts\Http\VersionParser;
use Jenky\LaravelAPI\Http\VersionParser\Header;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class HeaderVersioningTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // $app->get('config')->set('api.version_scheme', 'header');
        $app->rebinding(VersionParser::class, function ($app) {
            return new Header($app['config']);
        });
        dd($app->getBindings());
    }

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

    public function test_version_parser_is_header()
    {
        $this->assertInstanceOf(Header::class, $this->app[VersionParser::class]);
    }

    // public function test_api_default_version_header()
    // {
    //     $config = $this->app->get('config');

    //     $this->loadRoutes();

    //     $this->getJson('/api')
    //         ->assertOk()
    //         ->assertJson([
    //             'version' => [
    //                 'set' => $config->get('api.version'),
    //                 'route' => $config->get('api.version'),
    //             ],
    //         ]);
    // }

    // public function test_api_v1_header()
    // {
    //     $this->loadRoutes();

    //     $this->getJson('/api', ['Accept' => 'x.laravel.v1+json'])
    //         ->assertOk()
    //         ->assertJson([
    //             'version' => [
    //                 'set' => 'v1',
    //                 'route' => 'v1',
    //             ],
    //         ]);
    // }

    // public function test_api_v2_header()
    // {
    //     $this->loadRoutes();

    //     $this->getJson('/api', ['Accept' => 'x.laravel.v2+json'])
    //         ->assertOk()
    //         ->assertJson([
    //             'version' => [
    //                 'set' => 'v2',
    //                 'route' => 'v2',
    //             ],
    //         ]);
    // }
}
