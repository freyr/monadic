<?php

declare(strict_types=1);

namespace Freyr\Monadic;

/**
 * @template T
 */
abstract class Option
{
    /** @return bool */
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
     * @param T|callable(): T $default
     * @return T
     */
    abstract public function getOrElse($default);
}
