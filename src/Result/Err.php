<?php

declare(strict_types=1);

namespace Freyr\Monadic\Result;

use Freyr\Monadic\Result;

/**
 * @template T
 * @template E
 * @extends Result<T, E>
 */
final class Err extends Result
{
    /** @var E */
    private $error;

    /** @param E $error */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * @template U
     * @param callable(T): U $f
     * @return Result<T, E>
     */
    public function map(callable $f): Result
    {
        return new Err($this->error);
    }

    /**
     * @template U
     * @param callable(T): Result<U, E> $f
     * @return Result<T, E>
     */
    public function flatMap(callable $f): Result
    {
        return new Err($this->error);
    }

    /** @return E */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @template R
     * @param callable(T): R $ok
     * @param callable(E): R $err
     * @return R
     */
    public function fold(callable $ok, callable $err): mixed
    {
        return $err($this->error);
    }
}
