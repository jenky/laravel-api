# Laravel API

## Installation
Require this package with composer:

```
composer require jenky/laravel-api
```

Copy the package config to your local config with the publish command:

```
php artisan vendor:publish
```
or
```
php artisan vendor:publish --provider="Jenky\LaravelAPI\ApiServiceProvider"
```

## Usage
##### Create routes

**Route::api($version, $callback)**

```php
Route::api('v1', [
    'as' => 'api.v1.',
    'namespace' => 'API\v1',
], function () {
    // Your routes go here.
});
```

##### Response helpers
