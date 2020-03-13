<?php

namespace Jenky\LaravelAPI\Test\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

class HeaderVersioningTest extends FeatureTestCase
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

        $app->get('config')->set('api.version_scheme', 'header');
    }

    /**
     * Set up routes.
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

    /**
     * Set all headers before load routes.
     *
     * @return void
     */
    protected function setHeadersAndLoadRoutes(array $headers = [])
    {
        // Since createApplication in test case will use default request headers
        // and routes are loaded into memory so we need to replace all necessary
        // headers first before register the application routes
        if (! empty($headers)) {
            $this->app['request']->headers->add($headers);
        }

        $this->loadRoutes();
    }

    public function test_api_default_version_header()
    {
        $this->loadRoutes();

        $config = $this->app->get('config');

        $this->getJson('/api')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => $config->get('api.version'),
                    'request' => $config->get('api.version'),
                ],
            ]);
    }

    public function test_api_invalid_version_header()
    {
        $this->setHeadersAndLoadRoutes(['Accept' => 'application/x.laravel.v3+json']);

        $this->getJson('/api', ['Accept' => 'application/x.laravel.v3+json'])
            ->assertNotFound();
    }

    public function test_api_v1_header()
    {
        $this->setHeadersAndLoadRoutes(['Accept' => 'application/x.laravel.v1+json']);

        $this->getJson('/api', ['Accept' => 'application/x.laravel.v1+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v1',
                    'request' => 'v1',
                ],
            ]);
    }

    public function test_api_v2_header()
    {
        $this->setHeadersAndLoadRoutes(['Accept' => 'application/x.laravel.v2+json']);

        $this->getJson('/api', ['Accept' => 'application/x.laravel.v2+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'request' => 'v2',
                ],
            ]);
    }
}
