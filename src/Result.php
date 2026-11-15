<?php

declare(strict_types=1);

namespace Freyr\Monadic;

/**
 * @template-covariant T
 * @template-covariant E
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
     * @template F
     * @param callable(T): Result<U, F> $f
     * @return Result<U, E|F>
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
