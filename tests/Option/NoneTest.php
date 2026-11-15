<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option\None;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(None::class)]
final class NoneTest extends TestCase
{
    public static function defaultValue(): string
    {
        return 'from static method';
    }

    public function testIsDefined(): void
    {
        $none = new None();
        $this->assertFalse($none->isDefined());
    }

    public function testMapReturnsNone(): void
    {
        $none = new None();
        $this->assertInstanceOf(None::class, $none->map(fn ($x) => $x));
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
}
