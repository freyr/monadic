<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(None::class)]
final class NoneTest extends TestCase
{
    public static function defaultValue(): string
    {
        return 'from static method';
    }

    public function testIsDefinedOnNoneIsFalse(): void
    {
        $this->assertFalse((new None())->isDefined());
    }

    public function testMapOnNoneReturnsANone(): void
    {
        $mapped = (new None())->map(static fn (mixed $value): string => 'mapped');

        $this->assertInstanceOf(None::class, $mapped);
        $this->assertSame('default', $mapped->getOrElse('default'));
    }

    public function testMapOnNoneDoesNotCallTheFunction(): void
    {
        $function = static function (): never {
            self::fail('map() on None must not call the function.');
        };

        $this->assertInstanceOf(None::class, (new None())->map($function));
    }

    public function testFlatMapOnNoneReturnsANone(): void
    {
        $result = (new None())->flatMap(static fn (mixed $value): Some => new Some('inner'));

        $this->assertInstanceOf(None::class, $result);
        $this->assertSame('default', $result->getOrElse('default'));
    }

    public function testFlatMapOnNoneDoesNotCallTheFunction(): void
    {
        $this->assertInstanceOf(None::class, (new None())->flatMap(self::flatMapFunctionThatMustNotBeCalled(...)));
    }

    /**
     * @return Option<string>
     */
    private static function flatMapFunctionThatMustNotBeCalled(): Option
    {
        self::fail('flatMap() on None must not call the function.');
    }

    public function testGetOrElseOnNoneReturnsACallableStringUnchanged(): void
    {
        $this->assertSame('time', (new None())->getOrElse('time'));
    }

    public function testGetOrElseOnNoneReturnsACallableArrayUnchanged(): void
    {
        $this->assertSame(
            ['DateTimeImmutable', 'createFromFormat'],
            (new None())->getOrElse(['DateTimeImmutable', 'createFromFormat'])
        );
    }

    public function testGetOrElseOnNoneReturnsAClosureAsTheSameInstanceWithoutCallingIt(): void
    {
        $closure = static function (): never {
            self::fail('getOrElse() must not call a Closure default.');
        };

        $this->assertSame($closure, (new None())->getOrElse($closure));
    }

    public function testGetOrElseOnNoneReturnsAPlainDefault(): void
    {
        $this->assertSame(7, (new None())->getOrElse(7));
    }

    public function testGetOrCallOnNoneReturnsTheResultOfACalledClosure(): void
    {
        $this->assertSame('from closure', (new None())->getOrCall(static fn (): string => 'from closure'));
    }

    public function testGetOrCallOnNoneReturnsTheResultOfACalledStaticMethodArray(): void
    {
        $this->assertSame('from static method', (new None())->getOrCall([self::class, 'defaultValue']));
    }

    public function testGetOrCallOnNoneCallsTheDefaultExactlyOnce(): void
    {
        $calls = 0;
        (new None())->getOrCall(static function () use (&$calls): string {
            $calls++;

            return 'default';
        });

        $this->assertSame(1, $calls);
    }
}
