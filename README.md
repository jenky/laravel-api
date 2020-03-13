# Laravel API

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Test Status][ico-gh-tests]][link-gh-tests]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

The package provides a nice and easy way to define API routes and format JSON error response.

- [Laravel API](#laravel-api)
  - [Install](#install)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Usage](#usage)
    - [Register your routes](#register-your-routes)
    - [Error response](#error-response)
  - [Change log](#change-log)
  - [Testing](#testing)
  - [Contributing](#contributing)
  - [Security](#security)
  - [Credits](#credits)
  - [License](#license)

## Install

## Installation

You may use Composer to install this package into your Laravel project:

``` bash
$ composer require jenky/laravel-api
```

After installing, publish its assets using the `vendor:publish` Artisan command.

``` bash
php artisan vendor:publish
```

or

``` bash
php artisan vendor:publish --provider="Jenky\LaravelAPI\ApiServiceProvider"
```

## Configuration

After publishing Laravel API's assets, its primary configuration file will be located at `config/api.php`. This configuration file allows you to configure your api route and error response format and each configuration option includes a description of its purpose, so be sure to thoroughly explore this file.

## Usage

### Register your routes

Route API basically is the same as [Route Groups](https://laravel.com/docs/7.x/routing#route-groups). However it will add all necessary parameters to the route attributes based on the configuration. Because routes are grouped per version you can use the exact same URI to create a different response for the same endpoint.

``` php
Route::api('v1')
    ->group(function () {
        Route::get('/', 'TestController@v1');
    });

Route::api('v2')
    ->group(function () {
        Route::get('/', 'TestController@v2');
    });
```

This will generate the following with default configuration:

| Version scheme |   URI   |              Request Header             |                 Action                 |
|:--------------:|:-------:|:---------------------------------------:|:--------------------------------------:|
|      `uri`     | /api/v1 |                                         | App\Http\Controllers\TestController@v1 |
|      `uri`     | /api/v2 |                                         | App\Http\Controllers\TestController@v2 |
|    `header`    |   /api  | Accept: `application/x.laravel.v1+json` | App\Http\Controllers\TestController@v1 |
|    `header`    |   /api  | Accept: `application/x.laravel.v2+json` | App\Http\Controllers\TestController@v2 |

> For Header versioning, if the request doesn't have the `Accept` header with correct format then default version from config will be used.

### Error response

Dealing with errors when building an API can be a pain. Instead of manually building error responses you can simply throw an exception and the API will handle the response for you. Just add the trait `ExceptionResponse` to your `app/Exceptions/Handler` and the package will automatically catches the thrown exception and will convert it into its JSON representation.

``` php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jenky\LaravelAPI\Exception\ExceptionResponse;

class Handler extends ExceptionHandler
{
    use ExceptionResponse;
}
```

> You can change the default error response format in `config/api.php` file.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email contact@lynh.me instead of using the issue tracker.

## Credits

- [Lynh][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jenky/laravel-api.svg?style=flat
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat
[ico-travis]: https://img.shields.io/travis/com/jenky/laravel-api/master.svg?style=flat
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jenky/laravel-api.svg?style=flat
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jenky/laravel-api.svg?style=flat
[ico-downloads]: https://img.shields.io/packagist/dt/jenky/laravel-api.svg?style=flat
[ico-gh-tests]: https://github.com/jenky/laravel-api/workflows/Tests/badge.svg

[link-packagist]: https://packagist.org/packages/jenky/laravel-api
[link-travis]: https://travis-ci.com/jenky/laravel-api
[link-scrutinizer]: https://scrutinizer-ci.com/g/jenky/laravel-api/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jenky/laravel-api
[link-downloads]: https://packagist.org/packages/jenky/laravel-api
[link-author]: https://github.com/jenky
[link-contributors]: ../../contributors
[link-gh-tests]: https://github.com/jenky/laravel-api/actions
