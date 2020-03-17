# Changelog

All notable changes to `Laravel API` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed -->

## [6.4.0](https://github.com/jenky/laravel-api/compare/6.3.3...6.4.0) - 2020-03-17

### Added

- New middleware to force `Illuminate\Http\Request::expectsJson()` returns `true` if no appropriate header is set.
- Test with Github actions.

### Removed

- Remove `error` method from `ResponseMacros`.
- Drop support for Laravel `5.6` and below.

### Fixed

- Various bug fixes.
