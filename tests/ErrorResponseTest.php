<?php

namespace Jenky\LaravelAPI\Test;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ErrorResponseTest extends FeatureTestCase
{
    use WithFaker;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->get('config')->set('api.trace.as_string', true);
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
            ->group(function () {
                Route::middleware('auth:api')->get('user', function (Request $request) {
                    return $request->user();
                });

                Route::post('register', function (Request $request) {
                    $request->validate([
                        'email' => 'required|email',
                        'name' => 'required|min:2',
                        'password' => 'required|min:8|confirmed',
                    ]);

                    return response()->noContent();
                });

                Route::post('post', function () {
                    abort(403);
                });

                Route::put('post', function () {
                    throw new \InvalidArgumentException;
                });
            });
    }

    public function test_authentication()
    {
        $this->get('api/v1/user')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
                'status_code' => 401,
                'type' => 'AuthenticationException',
            ]);
    }

    public function test_validation()
    {
        $this->post('api/v1/register')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'status_code' => 422,
                'type' => 'ValidationException',
            ])
            ->assertJsonValidationErrors([
                'email', 'name', 'password',
            ]);

        $this->post('api/v1/register', [
            'email' => $this->faker()->email,
            'name' => $this->faker()->name,
            'password' => $password = Str::random(10),
            'password_confirmation' => $password,
        ])->assertStatus(204);
    }

    public function test_client_error()
    {
        $this->post('api/v1/post')
            ->assertForbidden()
            ->assertJson([
                'message' => 'Forbidden',
                'status_code' => 403,
                'type' => 'HttpException',
            ]);
    }

    public function test_server_error()
    {
        $this->put('api/v1/post')
            ->assertStatus(500)
            ->assertJson([
                'message' => 'Internal Server Error',
                'status_code' => 500,
                'type' => 'InvalidArgumentException',
            ]);
    }
}
