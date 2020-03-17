<?php

namespace Jenky\LaravelAPI\Test;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ResponseTest extends FeatureTestCase
{
    protected function getJsonStructure()
    {
        $structure = config('api.error_format', []);

        if (! $this->app['config']->get('app.debug')) {
            Arr::forget($structure, 'debug');
        }

        return $structure;
    }

    public function test_created_response()
    {
        Route::get('api/v1/created', function () {
            return response()->created(null, 'https://google.com');
        });

        $this->get('api/v1/created')
            ->assertStatus(201)
            ->assertHeader('Location', 'https://google.com');
    }

    public function test_accepted_response()
    {
        Route::get('api/v1/accepted', function () {
            return response()->accepted();
        });

        $this->get('api/v1/accepted')
            ->assertStatus(202);
    }

    public function test_not_found_response()
    {
        $structure = $this->getJsonStructure();
        Arr::forget($structure, 'errors');

        $this->get('api/v1/not-found')
            ->assertNotFound()
            ->assertJsonStructure(array_keys($structure))
            ->assertJson([
                'type' => 'NotFoundHttpException',
                'status_code' => 404,
            ]);
    }

    public function test_validation_errors_response()
    {
        Route::get('api/v1/errors', function () {
            request()->validate([
                'q' => 'required',
            ]);

            return ['ok' => true];
        });

        $this->get('api/v1/errors')
            ->assertStatus(422)
            ->assertJsonStructure(array_keys($this->getJsonStructure()))
            ->assertJsonValidationErrors([
                'q',
            ]);
    }

    public function test_error_response()
    {
        Route::get('api/v1/internal-error', function () {
            abort(500);
        });

        $structure = $this->getJsonStructure();
        Arr::forget($structure, 'errors');

        $this->get('api/v1/internal-error')
            ->assertStatus(500)
            ->assertJsonStructure(array_keys($structure))
            ->assertJson([
                'type' => 'HttpException',
                'status_code' => 500,
            ]);
    }
}
