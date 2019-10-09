<?php

namespace Jenky\LaravelAPI\Test;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Jenky\LaravelAPI\Test\FeatureTestCase;

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

    public function test_not_found_response()
    {
        $structure = $this->getJsonStructure();
        Arr::forget($structure, 'errors');

        $this->getJson('/api/not-found')
            ->assertNotFound()
            ->assertJsonStructure(array_keys($structure))
            ->assertJson([
                'type' => 'NotFoundHttpException',
                'status_code' => 404,
            ]);
    }

    public function test_validation_errors_response()
    {
        Route::get('api/errors', function () {
            request()->validate([
                'q' => 'required'
            ]);

            return ['ok' => true];
        });

        $this->getJson('/api/errors')
            ->assertStatus(422)
            ->assertJsonStructure(array_keys($this->getJsonStructure()))
            ->assertJsonValidationErrors([
                'q' => 'The q field is required.',
            ]);
    }

    public function test_error_response()
    {
        Route::get('api/internal-error', function () {
            abort(500);
        });

        $structure = $this->getJsonStructure();
        Arr::forget($structure, 'errors');

        $this->getJson('/api/internal-error')
            ->assertStatus(500)
            ->assertJsonStructure(array_keys($structure))
            ->assertJson([
                'type' => 'HttpException',
                'status_code' => 500,
            ]);
    }
}
