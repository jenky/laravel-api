<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default API Scheme
    |--------------------------------------------------------------------------
    |
    | A default scheme to use for your API.
    |
    | Supported: "prefix", "domain"
    |
    */

    'scheme' => env('API_SCHEME', 'prefix'),

    /*
    |--------------------------------------------------------------------------
    | Default API Prefix
    |--------------------------------------------------------------------------
    |
    | A default prefix to use for your API routes so you don't have to
    | specify it for each group.
    |
    */

    'prefix' => env('API_PREFIX', null),

    /*
    |--------------------------------------------------------------------------
    | Default API Domain
    |--------------------------------------------------------------------------
    |
    | A default domain to use for your API routes so you don't have to
    | specify it for each group.
    |
    */

    'domain' => env('API_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Default API Version
    |--------------------------------------------------------------------------
    |
    | This is the default version when strict mode is disabled and your API
    | is accessed via a web browser. It's also used as the default version
    | when generating your APIs documentation.
    |
    */

    'version' => env('API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | API class handlers.
    |--------------------------------------------------------------------------
    |
    | Here are each of the class handlers for the each exception.
    |
    */

    'handlers' => [
        'exception' => Jenky\LaravelAPI\Exception\Handler::class,
        'validation_exception' => Jenky\LaravelAPI\Exception\ValidationExceptionHandler::class,
    ],

];
