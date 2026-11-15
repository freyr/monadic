<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Types;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;
use RuntimeException;
use function PHPStan\Testing\assertType;

/*
 * Type assertions for PHPStan only. PHPUnit does not run this file
 * (no *Test.php suffix); `composer stan` analyses it and fails on a mismatch.
 */

/**
 * Declares its return type, so the assertion on flatMap() does not depend on
 * how precisely PHPStan infers the type of a closure body.
 *
 * @return Result<float, string>
 */
function halfOrError(int $value): Result
{
    return $value > 0 ? new Ok($value / 2.0) : new Err('not positive');
}

/**
 * @param Option<int> $option
 */
function acceptsIntOption(Option $option): void
{
}

/**
 * @param Result<int, string> $result
 */
function acceptsIntStringResult(Result $result): void
{
}

function optionTypes(int $value, string $fallback): void
{
    $some = new Some($value);
    assertType('Freyr\Monadic\Option\Some<int>', $some);
    assertType('Freyr\Monadic\Option<string>', $some->map(fn (int $x): string => $x > 0 ? $fallback : ''));
    assertType('Freyr\Monadic\Option<float>', $some->flatMap(fn (int $x): Option => new Some($x / 2.0)));
    assertType('int|string', $some->getOrElse($fallback));
    assertType('(Closure(): bool)|int', $some->getOrElse(fn (): bool => $value > 0));
    assertType('bool|int', $some->getOrCall(fn (): bool => $value > 0));

    $none = new None();
    assertType('Freyr\Monadic\Option\None', $none);
    assertType('string', $none->getOrElse($fallback));
    assertType("'time'", $none->getOrElse('time'));
    assertType('string', $none->getOrCall(fn (): string => $fallback));
    acceptsIntOption($none);
    acceptsIntOption($some);
}

/**
 * @param Option<int> $option
 */
function getOrElseOnOption(Option $option, string $fallback): void
{
    assertType('int|string', $option->getOrElse($fallback));
    assertType('(Closure(): string)|int', $option->getOrElse(fn (): string => $fallback));
    assertType('int|string', $option->getOrCall(fn (): string => $fallback));
}

function resultTypes(int $value, string $message): void
{
    $ok = new Ok($value);
    assertType('Freyr\Monadic\Result\Ok<int>', $ok);
    assertType('Freyr\Monadic\Result<string, never>', $ok->map(fn (int $x): string => $x > 0 ? $message : ''));
    assertType('Freyr\Monadic\Result<float, string>', $ok->flatMap(halfOrError(...)));
    assertType('int|string', $ok->fold(fn (int $x): int => $x, fn (): string => $message));

    $err = new Err($message);
    assertType('Freyr\Monadic\Result\Err<string>', $err);
    assertType('Freyr\Monadic\Result<int, string>', $err->map(fn (): int => 1));
    assertType('string', $err->getError());
    acceptsIntStringResult($ok);
    acceptsIntStringResult($err);
}

/**
 * @param Result<int, string> $result
 * @param callable(int): Result<bool, RuntimeException> $f
 */
function errorTypeWidensInFlatMap(Result $result, callable $f): void
{
    assertType('Freyr\Monadic\Result<bool, RuntimeException|string>', $result->flatMap($f));
    assertType('Freyr\Monadic\Result<int, string>', $result->map(fn (int $x): int => $x + 1));
    assertType('bool|int', $result->fold(fn (int $x): int => $x, fn (string $e): bool => $e === ''));
}
