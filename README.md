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
#### Important
Please remove the `api` prefix in the `mapApiRoutes` method from `App\Provivers\RouteServiceProvider`.
```php
/**
 * Define the "api" routes for the application.
 *
 * These routes are typically stateless.
 *
 * @return void
 */
protected function mapApiRoutes()
{
    Route::middleware('api')
         // ->prefix('api') Remove or comment this line.
         ->namespace($this->namespace)
         ->group(base_path('routes/api.php'));
}
```

#### Create routes

**Route::api($version, array $options, $callback)**

The `options` parameter can be omitted from the method.

```php
Route::api('v1', [
    'as' => 'api.v1.',
    'namespace' => 'API\v1',
], function () {
    // Your routes go here.
});
```

#### Response helpers
