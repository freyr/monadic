<?php

declare(strict_types=1);

namespace Freyr\Monadic\Result;

use Freyr\Monadic\Result;

/**
 * @template T
 * @template E
 * @extends Result<T, E>
 */
final class Ok extends Result
{
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @template U
     * @param callable(T): U $f
     * @return Result<U, E>
     */
    public function map(callable $f): Result
    {
        return new Ok($f($this->value));
    }

    /**
     * @template U
     * @param callable(T): Result<U, E> $f
     * @return Result<U, E>
     */
    public function flatMap(callable $f): Result
    {
        return $f($this->value);
    }

    /** @return T */
    public function get()
    {
        return $this->value;
    }

    /**
     * @template R
     * @param callable(T): R $ok
     * @param callable(E): R $err
     * @return R
     */
    public function fold(callable $ok, callable $err): mixed
    {
        return $ok($this->value);
    }
}
