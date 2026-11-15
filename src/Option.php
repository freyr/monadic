<?php

declare(strict_types=1);

namespace Freyr\Monadic;

/**
 * @template-covariant T
 */
abstract class Option
{
    abstract public function isDefined(): bool;

    /**
     * @template U
     * @param callable(T): U $f
     * @return Option<U>
     */
    abstract public function map(callable $f): Option;

    /**
     * @template U
     * @param callable(T): Option<U> $f
     * @return Option<U>
     */
    abstract public function flatMap(callable $f): Option;

    /**
     * Returns the held value, or the default exactly as given. Never calls the default.
     *
     * @template D
     * @param D $default
     * @return T|D
     */
    abstract public function getOrElse(mixed $default): mixed;

    /**
     * Returns the held value, or the result of calling the default.
     *
     * @template D
     * @param callable(): D $default
     * @return T|D
     */
    abstract public function getOrCall(callable $default): mixed;
}
