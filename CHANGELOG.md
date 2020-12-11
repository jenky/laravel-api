# Changelog

All notable changes to `Laravel API` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed -->


## [7.1.2](https://github.com/jenky/laravel-api/compare/7.1.1...7.1.2) - 2020-12-11

### Added
- Added support PHP 8.

## [7.1.1](https://github.com/jenky/laravel-api/compare/7.1.0...7.1.1) - 2020-10-23

### Changed
- Improve exception handler trait.

## [7.1.0](https://github.com/jenky/laravel-api/compare/7.0.0...7.1.0) - 2020-09-15

### Added
- Laravel 8 support.

## [7.0.0](https://github.com/jenky/laravel-api/compare/6.4.2...7.0.0) - 2020-06-29

### Added
- Laravel 7 support.

### Changed
- Change `version_scheme` config value from `prefix` to `uri`. Using `prefix` is not supported.
- Rename `ExceptionWithErrors` to `ErrorException` and move it to `Contracts\Exception` folder.

### Removed
- Remove `api` macro from `Route`.
- Remove `ExceptionWithType` interface.
- Drop support for Laravel `6.x` and below.

## [6.4.2](https://github.com/jenky/laravel-api/compare/6.4.1...6.4.2) - 2020-04-01

### Changed
- Both `ExceptionWithErrors` and `ExceptionWithType` now extend `Throwable`.

## [6.4.1](https://github.com/jenky/laravel-api/compare/6.4.0...6.4.1) - 2020-03-26

### Changed
- Force return type `array` for `getErrors` method in `ExceptionWithErrors`.

### Removed
- Remove `setType` method from `ExceptionWithType`.
- Remove `setErrors` method from `ExceptionWithErrors`.

## [6.4.0](https://github.com/jenky/laravel-api/compare/6.3.3...6.4.0) - 2020-03-17

### Added
- New middleware to force `Illuminate\Http\Request::expectsJson()` returns `true` if no appropriate header is set.
- Test with Github actions.

### Removed
- Remove `error` method from `ResponseMacros`.
- Drop support for Laravel 5.6 and below.

### Fixed
- Various bug fixes.
