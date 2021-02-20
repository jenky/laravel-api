<?php

namespace Jenky\LaravelAPI\Tests;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Tests\Fixtures\OauthException;
use Jenky\LaravelAPI\Tests\Fixtures\ProcessingException;
use Throwable;

class CustomExceptionTest extends FeatureTestCase
{
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
            ->group(function () {
                Route::get('exception-type', function () {
                    throw (new OauthException(400, 'The grant type is not available for your client!'))
                        ->setType('oauth');
                });

                Route::get('exception-errors', function () {
                    throw (new ProcessingException('Unable to process your request.', 1234))
                        ->setErrors([
                            'cache' => 'node is offline',
                            'queue' => 'under heavy load',
                        ]);
                });

                Route::get('exception-callback', function () {
                    throw new AuthenticationException;
                });
            });
    }

    public function test_exception_has_type()
    {
        $this->get('api/v1/exception-type')
            ->assertStatus(400)
            ->assertJson([
                'message' => 'The grant type is not available for your client!',
                'status_code' => 400,
                'type' => 'oauth',
            ]);
    }

    public function test_exceptions_has_errors()
    {
        $this->get('api/v1/exception-errors')
            ->assertStatus(500)
            ->assertJson([
                'message' => 'Unable to process your request.',
                'status_code' => 500,
                'code' => 1234,
                // 'errors' => [],
            ]);
    }

    public function test_exception_callback()
    {
        $this->get('api/v1/exception-callback')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
                'type' => 'AuthenticationException',
                'status_code' => 401,
            ]);

        $this->app->make(ExceptionHandler::class)->catch(AuthenticationException::class, function (Throwable $e, $handler) {
            $handler->replace('type', 'authentication_error')
                ->replace('message', 'Authentication failed.');
        });

        $this->get('api/v1/exception-callback')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Authentication failed.',
                'type' => 'authentication_error',
                'status_code' => 401,
            ]);

        $this->app->make(ExceptionHandler::class)->catch(function (AuthenticationException $e, $handler) {
            $handler->setReplacements([
                ':code' => 1001,
                ':type' => 'invalid_credentials',
            ]);
        });

        $this->get('api/v1/exception-callback')
            ->assertStatus(401)
            ->assertJson([
                'code' => 1001,
                'type' => 'invalid_credentials',
                'status_code' => 401,
            ]);
    }
}
