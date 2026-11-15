<?php

declare(strict_types=1);

namespace Freyr\Monadic\Result;

use Freyr\Monadic\Result;

/**
 * @template-covariant T
 * @extends Result<T, never>
 */
final class Ok extends Result
{
    /**
     * @param T $value
     */
    public function __construct(
        private readonly mixed $value
    ) {
    }

    /**
     * @template U
     * @param callable(T): U $f
     * @return Result<U, never>
     */
    public function map(callable $f): Result
    {
        return new Ok($f($this->value));
    }

    /**
     * @template U
     * @template F
     * @param callable(T): Result<U, F> $f
     * @return Result<U, F>
     */
    public function flatMap(callable $f): Result
    {
        return $f($this->value);
    }

    /**
     * @return T
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @template R
     * @param callable(T): R $ok
     * @param callable(never): R $err
     * @return R
     */
    public function fold(callable $ok, callable $err): mixed
    {
        return $ok($this->value);
    }
}
