<?php

declare(strict_types=1);

namespace Freyr\Monadic\Option;

use Freyr\Monadic\Option;

/**
 * @template T
 * @extends Option<T>
 */
final class None extends Option
{
    public function isDefined(): bool
    {
        return false;
    }

    /**
     * @template U
     * @param callable(T): U $f
     * @return Option<U>
     */
    public function map(callable $f): Option
    {
        return new None();
    }

    /**
     * @template U
     * @param callable(T): Option<U> $f
     * @return Option<U>
     */
    public function flatMap(callable $f): Option
    {
        return new None();
    }

    /**
     * @param T|callable(): T $default
     * @return T
     */
    public function getOrElse($default)
    {
        return is_callable($default) ? $default() : $default;
    }
}
