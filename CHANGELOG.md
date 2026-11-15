# Changelog

## [Unreleased]
### Changed
- BREAKING: `Option::getOrElse()` returns the default exactly as given and no longer calls a callable default. Use `getOrCall()` for a lazy default.

### Added
- `Option::getOrCall()`, which calls the default and returns its result when the option is `None`.

### Fixed
- `None::getOrElse()` no longer calls a default that is a function-name string or a `[class, method]` array; `getOrElse('time')` returns `'time'`.

## [0.1.0] - 2025-05-23
### Added
- Initial setup of the Freyr Monadic library with monadic types `Option` and `Result`.
- Configuration files for PHP CS Fixer, PHPStan, and Easy Coding Standard.
- PHPUnit configuration for running tests.
- Makefile for Docker container management.
- MIT License.
- README documentation.