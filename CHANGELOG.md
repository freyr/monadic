# Changelog

## [Unreleased]
### Changed
- BREAKING: `Option::getOrElse()` returns the default exactly as given and no longer calls a callable default. Use `getOrCall()` for a lazy default.
- Correct generic types: `@template-covariant` on `Option`, `Result`, `Some`, `Ok` and `Err`; `None` is `Option<never>`, `Ok<T>` is `Result<T, never>` and `Err<E>` is `Result<never, E>`.
- Native `mixed` types and `readonly` properties in the classes.

### Added
- `Option::getOrCall()`, which calls the default and returns its result when the option is `None`.
- Support for PHP 8.5. The library is tested on PHP 8.4 and PHP 8.5.
- A runnable example, `examples/user-service.php` (`composer example`).

### Fixed
- `None::getOrElse()` no longer calls a default that is a function-name string or a `[class, method]` array; `getOrElse('time')` returns `'time'`.

### Internal
- Development packages: PHPUnit 13, Easy Coding Standard 13 and PHPStan 2 at level max. PHP CS Fixer is removed.
- GitHub Actions run the tests, PHPStan and ECS on each pull request, on PHP 8.4 and PHP 8.5.
- Infection mutation testing, and a 100% line-coverage limit (`composer coverage`).
- Releases follow the `freyr/.github` release standard: the pull request title sets the version, and each merge to `main` creates a tag `vX.Y.Z` and a GitHub release.

## [0.1.0] - 2025-05-23
### Added
- Initial setup of the Freyr Monadic library with monadic types `Option` and `Result`.
- Configuration files for PHP CS Fixer, PHPStan, and Easy Coding Standard.
- PHPUnit configuration for running tests.
- Makefile for Docker container management.
- MIT License.
- README documentation.