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
    protected function loadRoutes($version = null)
    {
        $request = $this->app->make('request');
        $config = $this->app->make('config');
        $version = $version ?: $config->get('api.version');
// dump($request->version(), $version);
        if ($request->version() != $version) {
            return;
        }

        Route::get('api', function () use ($version) {
            return $this->getResponseBody($version);
        });
    }

    /**
     * Replace request "Accept" header value.
     *
     * @return self
     */
    protected function replaceAcceptHeader($value)
    {
        // Since createApplication in test case will use default request headers
        // and routes are loaded into memory so we need to replace "Accept" header
        // before register the application routes
        $this->app->make('request')->headers->set('Accept', $value);

        return $this;
    }

    public function test_api_default_version_header()
    {
        $config = $this->app->make('config');

        $this->loadRoutes();

        $this->get('/api')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => $version = $config->get('api.version'),
                    'request' => $version,
                ],
            ]);
    }

    public function test_api_invalid_version_header()
    {
        $this->replaceAcceptHeader('application/x.laravel.v3+json')
            ->loadRoutes();

        $this->get('/api', ['Accept' => 'application/x.laravel.v3+json'])
            ->assertNotFound();
    }

    public function test_api_v1_header()
    {
        $this->replaceAcceptHeader('application/x.laravel.v1+json')
            ->loadRoutes('v1');

        $this->get('/api', ['Accept' => 'application/x.laravel.v1+json'])
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
        $this->replaceAcceptHeader('application/x.laravel.v2+json')
            ->loadRoutes('v2');

        $this->get('/api', ['Accept' => 'application/x.laravel.v2+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'request' => 'v2',
                ],
            ]);
    }
}
