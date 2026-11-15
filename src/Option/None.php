<?php

declare(strict_types=1);

namespace Freyr\Monadic\Option;

use Freyr\Monadic\Option;

/**
 * @extends Option<never>
 */
final class None extends Option
{
    public function isDefined(): bool
    {
        return false;
    }

    /**
     * @template U
     * @param callable(never): U $f
     * @return Option<U>
     */
    public function map(callable $f): Option
    {
        return new None();
    }

    /**
     * @template U
     * @param callable(never): Option<U> $f
     * @return Option<U>
     */
    public function flatMap(callable $f): Option
    {
        return new None();
    }

    /**
     * @template D
     * @param D|(callable(): D) $default
     * @return D
     */
    public function getOrElse(mixed $default): mixed
    {
        return is_callable($default) ? $default() : $default;
    }
}
