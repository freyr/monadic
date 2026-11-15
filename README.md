# Freyr Monadic Library

Freyr Monadic is a PHP library designed to provide monadic types, specifically `Option` and `Result`, to facilitate functional programming paradigms in PHP. 
This library aims to enhance code reliability and readability by offering a structured approach to handling optional values and results of computations.

## Features
- **Option Monad**: Represents optional values, allowing for safe chaining and transformation of computations.
- **Result Monad**: Encapsulates successful or failed computations, providing a clear and concise way to handle errors.

## Requirements
PHP 8.4 or PHP 8.5.

## Installation
To install this library, use Composer:
```
composer require freyr/monadic
```

## Usage

### Option

An `Option` is either `Some` (holds a value) or `None` (holds nothing).

```php
use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;

/** @return Option<string> */
function findName(int $id): Option
{
    $names = [1 => 'Alice', 2 => 'Bob'];

    return isset($names[$id]) ? new Some($names[$id]) : new None();
}

findName(1)->isDefined();                                    // true
findName(99)->isDefined();                                   // false
findName(1)->map(strtoupper(...))->getOrElse('nobody');      // 'ALICE'
findName(99)->map(strtoupper(...))->getOrElse('nobody');     // 'nobody'
```

`flatMap()` takes a function that returns an `Option`, so the result is not nested:

```php
/** @return Option<string> */
function onlyLongName(string $name): Option
{
    return strlen($name) > 3 ? new Some($name) : new None();
}

findName(1)->flatMap(onlyLongName(...))->getOrElse('none');  // 'Alice'
findName(2)->flatMap(onlyLongName(...))->getOrElse('none');  // 'none'
```

`getOrElse($default)` and `getOrCall(callable $default)` differ:

- `getOrElse($default)` returns the default exactly as given. It never calls the default,
  even when the default is callable. `(new None())->getOrElse('time')` returns the string `'time'`.
- `getOrCall(callable $default)` calls the default only when the option is `None`
  and returns its result. Use it when the default is expensive to compute.

```php
findName(99)->getOrElse('guest');                            // 'guest'
findName(99)->getOrCall(fn (): string => 'guest');           // 'guest', computed now
findName(1)->getOrCall(fn (): string => 'guest');            // 'Alice', the function is not called
```

### Result

A `Result` is either `Ok` (a success value) or `Err` (an error value).

```php
use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;

/** @return Result<string, string> */
function sendEmail(string $name): Result
{
    return $name === 'Carol'
        ? new Err('Email service error for ' . $name)
        : new Ok('Email sent to ' . $name);
}

/** @return Result<string, string> */
function confirm(string $message): Result
{
    return new Ok($message . ' (confirmed)');
}

$show = fn (Result $result): string => $result->fold(
    fn (string $message): string => 'Success: ' . $message,
    fn (string $error): string => 'Failure: ' . $error,
);

$show(sendEmail('Alice'));                                   // 'Success: Email sent to Alice'
$show(sendEmail('Carol'));                                   // 'Failure: Email service error for Carol'
$show(sendEmail('Alice')->map(strtoupper(...)));             // 'Success: EMAIL SENT TO ALICE'
$show(sendEmail('Alice')->flatMap(confirm(...)));            // 'Success: Email sent to Alice (confirmed)'
$show(sendEmail('Carol')->flatMap(confirm(...)));            // 'Failure: Email service error for Carol'
```

`map()` changes the success value. `flatMap()` takes a function that returns a `Result`,
so a failure stops the chain. An `Err` skips `map()` and `flatMap()` and keeps its error.

Read a `Result` through `fold($ok, $err)`: it calls the first function for `Ok` and the
second function for `Err`. `Ok::get()` and `Err::getError()` exist only on the concrete
classes, not on `Result`.

### Full example

`examples/user-service.php` is a runnable example that chains `Option` and `Result`
in a small user service (find a user, keep adults, send an email, fold to a message).
Run it with `composer example` or `make example`.

## Development

The tools run in Docker. Each command below runs in the `php` container.

| Command | What it does |
|---|---|
| `make install` | Installs the dependencies (`composer update`). |
| `make check` | Runs the tests (PHPUnit), PHPStan (level max) and ECS. |
| `make coverage` | Runs the tests with coverage and fails when line coverage is below 100%. |
| `make infection` | Runs mutation testing (Infection). Minimum MSI is 100%, with uncovered code included. |
| `make example` | Runs `examples/user-service.php`. |

The container uses PHP 8.5 by default. To build it for PHP 8.4:

```
PHP_VERSION=8.4 docker compose build php
```

### Releases

The repo follows the [release standard](https://github.com/freyr/.github#readme) of the `freyr/.github` repo. The pull request title is a Conventional Commits title, and it decides the version. Before 1.0.0, `feat!:` (or `BREAKING CHANGE:` in the pull request body) gives a minor version; `feat:`, `fix:`, `perf:` and `revert:` give a patch version; `docs:`, `test:`, `ci:`, `build:`, `refactor:`, `style:` and `chore:` give no release. A pull request that changes only `tests/`, `examples/`, `tools/` or `*.md` files gives no release. A merge to `main` that gives a release creates the tag `vX.Y.Z` and the GitHub release, and Packagist reads the version from the tag.

## Educational Purpose
This library was created for educational purposes to demonstrate the implementation and use of monadic types in PHP. It is intended to help developers understand and apply functional programming concepts in their PHP projects.

## License
This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
