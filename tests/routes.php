<?php

Route::api('v1')
    ->prefix('v1')
    ->group(function () {
        Route::get('/', function () {
            return [
                'version' => [
                    'set' => 'v1',
                    'route' => request()->route()->version(),
                ],
            ];
        });
    });

Route::api('v2')
    ->prefix('v2')
    ->group(function () {
        Route::get('/', function () {
            return [
                'version' => [
                    'set' => 'v2',
                    'route' => request()->route()->version(),
                ],
            ];
        });
    });
