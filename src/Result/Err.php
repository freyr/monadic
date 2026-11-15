<?php

declare(strict_types=1);

namespace Freyr\Monadic\Result;

use Freyr\Monadic\Result;

/**
 * @template-covariant E
 * @extends Result<never, E>
 */
final class Err extends Result
{
    /**
     * @param E $error
     */
    public function __construct(
        private readonly mixed $error
    ) {
    }

    /**
     * @template U
     * @param callable(never): U $f
     * @return Result<U, E>
     */
    public function map(callable $f): Result
    {
        return new Err($this->error);
    }

    /**
     * @template U
     * @template F
     * @param callable(never): Result<U, F> $f
     * @return Result<U, E|F>
     */
    public function flatMap(callable $f): Result
    {
        return new Err($this->error);
    }

    /**
     * @return E
     */
    public function getError(): mixed
    {
        return $this->error;
    }

    /**
     * @template R
     * @param callable(never): R $ok
     * @param callable(E): R $err
     * @return R
     */
    public function fold(callable $ok, callable $err): mixed
    {
        return $err($this->error);
    }
}
