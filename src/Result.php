<?php

declare(strict_types=1);

namespace Freyr\Monadic;

/**
 * @template T
 * @template E
 */
abstract class Result
{
    /**
     * @template U
     * @param callable(T): U $f
     * @return Result<U, E>
     */
    abstract public function map(callable $f): Result;

    /**
     * @template U
     * @param callable(T): Result<U, E> $f
     * @return Result<U, E>
     */
    abstract public function flatMap(callable $f): Result;

    /**
     * @template R
     * @param callable(T): R $ok
     * @param callable(E): R $err
     * @return R
     */
    abstract public function fold(callable $ok, callable $err): mixed;
}
