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

                    return [];
                });
            });
    }

    public function test_authentication()
    {
        $this->getJson('api/v1/user')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
                'status_code' => 401,
                'type' => 'AuthenticationException',
            ]);
    }

    public function test_validation()
    {
        $this->postJson('api/v1/register')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'status_code' => 422,
                'type' => 'ValidationException',
            ])
            ->assertJsonValidationErrors([
                'email', 'name', 'password',
            ]);

        $this->postJson('api/v1/register', [
            'email' => $this->faker()->email,
            'name' => $this->faker()->name,
            'password' => $password = Str::random(10),
            'password_confirmation' => $password,
        ])->assertOk();
    }
}
