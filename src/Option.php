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
     * @template D
     * @param D|(callable(): D) $default
     * @return T|D
     */
    abstract public function getOrElse(mixed $default): mixed;
}
