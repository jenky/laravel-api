<?php

namespace Jenky\LaravelAPI\Tests\Route;

use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Tests\FeatureTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

        $app->get('config')->set('api.uri_scheme', 'domain');
        $app->get('config')->set('api.domain', 'api');
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

        if ($request->version() != $version) {
            return;
        }

        Route::domain('api.localhost')->group(function () use ($version) {
            Route::get('/', function () use ($version) {
                return $this->getResponseBody($version);
            });
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

        $this->get('http://api.localhost')
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => $version = $config->get('api.version'),
                    'request' => $version,
                ],
            ]);
    }

    public function test_api_strict_default_version_header()
    {
        $this->app->make('config')->set('api.strict', true);

        $this->expectException(BadRequestHttpException::class);

        $this->loadRoutes();

        $this->get('http://api.localhost')
            ->assertStatus(400);
    }

    public function test_api_invalid_version_header()
    {
        $this->replaceAcceptHeader('application/x.laravel.v3+json')
            ->loadRoutes();

        $this->get('http://api.localhost', ['Accept' => 'application/x.laravel.v3+json'])
            ->assertNotFound();
    }

    public function test_api_v1_header()
    {
        $this->replaceAcceptHeader('application/x.laravel.v1+json')
            ->loadRoutes('v1');

        $this->get('http://api.localhost', ['Accept' => 'application/x.laravel.v1+json'])
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

        $this->get('http://api.localhost', ['Accept' => 'application/x.laravel.v2+json'])
            ->assertOk()
            ->assertJson([
                'version' => [
                    'set' => 'v2',
                    'request' => 'v2',
                ],
            ]);
    }
}
