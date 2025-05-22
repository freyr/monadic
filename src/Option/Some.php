<?php

declare(strict_types=1);

namespace Freyr\Monadic\Option;

use Freyr\Monadic\Option;

/**
 * @template T
 * @extends Option<T>
 */
final class Some extends Option
{
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value)
    {
        $this->value = $value;
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
     * @param T|callable(): T $default
     * @return T
     */
    public function getOrElse($default)
    {
        return $this->value;
    }
}
