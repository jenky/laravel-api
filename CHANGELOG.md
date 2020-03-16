# Changelog

All notable changes to `Laravel API` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

<!-- ## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed -->

## [Unreleased]

### Added

- Supports Laravel 7 and drops support for Laravel < 7.
- New middleware to force `Illuminate\Http\Request::expectsJson()` returns `true` if no appropriate header is set.
- Tests with Github actions.

### Removed

- Remove `error` method from `ResponseMacros`.
- Remove `api` macro from `Route`.

### Fixed

- Various bug fixes.
