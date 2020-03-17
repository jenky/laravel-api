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

- Laravel 7 support.
- New middleware to force `Illuminate\Http\Request::expectsJson()` returns `true` if no appropriate header is set.
- Test with Github actions.

### Removed

- Remove `error` method from `ResponseMacros`.
- Remove `api` macro from `Route`.
- Drop support for Laravel `6.x` and below

### Fixed

- Various bug fixes.
