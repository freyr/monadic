<?php

declare(strict_types=1);

namespace Freyr\Monadic\Option;

use Freyr\Monadic\Option;

/**
 * @template-covariant T
 * @extends Option<T>
 */
final class Some extends Option
{
    /**
     * @param T $value
     */
    public function __construct(
        private readonly mixed $value
    ) {
    }

    public function isDefined(): bool
    {
        return true;
    }

    /**
     * @template U
     * @param callable(T): U $f
     * @return Option<U>
     */
    public function map(callable $f): Option
    {
        return new Some($f($this->value));
    }

    /**
     * @template U
     * @param callable(T): Option<U> $f
     * @return Option<U>
     */
    public function flatMap(callable $f): Option
    {
        return $f($this->value);
    }

    /**
     * @template D
     * @param D|(callable(): D) $default
     * @return T|D
     */
    public function getOrElse(mixed $default): mixed
    {
        return $this->value;
    }
}
