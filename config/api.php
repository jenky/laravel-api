<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default API URI Scheme
    |--------------------------------------------------------------------------
    |
    | A default URI scheme to use for your API.
    |
    | Supported: "prefix", "domain"
    |
    */

    'uri_scheme' => env('API_URI_SCHEME', 'prefix'),

    /*
    |--------------------------------------------------------------------------
    | Default API Version Scheme
    |--------------------------------------------------------------------------
    |
    | A default versioning scheme to use for your API.
    |
    | Supported: "prefix", "header"
    |
    */

    'version_scheme' => env('API_VERSION_SCHEME', 'prefix'),

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
    | Standards Tree
    |--------------------------------------------------------------------------
    |
    | Versioning an API with Dingo revolves around content negotiation and
    | custom MIME types. A custom type will belong to one of three
    | standards trees, the Vendor tree (vnd), the Personal tree
    | (prs), and the Unregistered tree (x).
    |
    | By default the Unregistered tree (x) is used, however, should you wish
    | to you can register your type with the IANA. For more details:
    | https://tools.ietf.org/html/rfc6838
    |
    */

    'standardsTree' => env('API_STANDARDS_TREE', 'x'),

    /*
    |--------------------------------------------------------------------------
    | API Subtype
    |--------------------------------------------------------------------------
    |
    | Your subtype will follow the standards tree you use when used in the
    | "Accept" header to negotiate the content type and version.
    |
    | For example: Accept: application/x.SUBTYPE.v1+json
    |
    */

    'subtype' => env('API_SUBTYPE', 'laravel'),

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
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | Enabling strict mode will require clients to send a valid Accept header
    | with every request. This also voids the default API version, meaning
    | your API will not be browsable via a web browser.
    |
    */

    'strict' => env('API_STRICT', false),

    /*
    |--------------------------------------------------------------------------
    | Generic Error Format
    |--------------------------------------------------------------------------
    |
    | When some HTTP exceptions are not caught and dealt with the API will
    | generate a generic error response in the format provided. Any
    | keys that aren't replaced with corresponding values will be
    | removed from the final response.
    |
    */

    'errorFormat' => [
        'message' => ':message',
        'type' => ':type',
        'status_code' => ':status_code',
        'errors' => ':errors',
        'code' => ':code',
        'debug' => ':debug',
    ],

    'trace' => [
        'as_string' => false,
        'include_args' => false,
    ],
];
