<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Some::class)]
final class SomeTest extends TestCase
{
    public function testIsDefinedOnSomeIsTrue(): void
    {
        $this->assertTrue((new Some(42))->isDefined());
    }

    public function testIsDefinedOnSomeHoldingNullIsTrue(): void
    {
        $this->assertTrue((new Some(null))->isDefined());
    }

    public function testGetOrElseOnSomeHoldingNullReturnsNullNotTheDefault(): void
    {
        $this->assertNull((new Some(null))->getOrElse('default'));
    }

    public function testGetOrElseOnSomeHoldingZeroReturnsZeroNotTheDefault(): void
    {
        $this->assertSame(0, (new Some(0))->getOrElse('default'));
    }

    public function testMapOnSomeReturnsASomeThatHoldsTheReturnedValue(): void
    {
        $mapped = (new Some(42))->map(static fn (int $value): int => $value + 1);

        $this->assertInstanceOf(Some::class, $mapped);
        $this->assertSame(43, $mapped->getOrElse('none'));
    }

    public function testMapOnSomePassesExactlyTheHeldValueToTheFunction(): void
    {
        $mapped = (new Some(42))->map(static fn (mixed ...$arguments): array => $arguments);

        $this->assertSame([42], $mapped->getOrElse('none'));
    }

    public function testMapOnSomeToNullReturnsASomeThatHoldsNull(): void
    {
        $mapped = (new Some(42))->map(static fn (int $value): null => null);

        $this->assertTrue($mapped->isDefined());
        $this->assertNull($mapped->getOrElse('none'));
    }

    public function testFlatMapOnSomeReturnsExactlyTheSomeThatTheFunctionReturns(): void
    {
        $returned = new Some('inner');

        $this->assertSame($returned, (new Some(42))->flatMap(static fn (int $value): Option => $returned));
    }

    public function testFlatMapOnSomeReturnsExactlyTheNoneThatTheFunctionReturns(): void
    {
        $returned = new None();

        $this->assertSame($returned, (new Some(42))->flatMap(static fn (int $value): Option => $returned));
    }

    public function testFlatMapOnSomePassesExactlyTheHeldValueToTheFunction(): void
    {
        $result = (new Some(42))->flatMap(static fn (mixed ...$arguments): Option => new Some($arguments));

        $this->assertSame([42], $result->getOrElse('none'));
    }

    public function testGetOrElseOnSomeReturnsTheHeldValueNotTheDefault(): void
    {
        $this->assertSame(42, (new Some(42))->getOrElse(0));
    }

    public function testGetOrCallOnSomeReturnsTheHeldValueWithoutCallingTheDefault(): void
    {
        $default = static function (): never {
            self::fail('getOrCall() on Some must not call the default.');
        };

        $this->assertSame(42, (new Some(42))->getOrCall($default));
    }

    public function testGetOrCallOnSomeHoldingNullReturnsNullWithoutCallingTheDefault(): void
    {
        $this->assertNull((new Some(null))->getOrCall(self::stringDefaultThatMustNotBeCalled(...)));
    }

    private static function stringDefaultThatMustNotBeCalled(): string
    {
        self::fail('getOrCall() on Some holding null must not call the default.');
    }

    public function testGetOrCallOnSomeHoldingZeroReturnsZeroWithoutCallingTheDefault(): void
    {
        $default = static function (): never {
            self::fail('getOrCall() on Some holding zero must not call the default.');
        };

        $this->assertSame(0, (new Some(0))->getOrCall($default));
    }

    public function testMapOnSomeToANoneReturnsASomeThatHoldsThatNone(): void
    {
        $inner = new None();
        $mapped = (new Some(42))->map(static fn (int $value): None => $inner);

        $this->assertInstanceOf(Some::class, $mapped);
        $this->assertSame($inner, $mapped->getOrElse('default'));
    }

    public function testMapOnSomeHoldingNullPassesNullToTheFunction(): void
    {
        $mapped = (new Some(null))->map(static fn (mixed ...$arguments): array => $arguments);

        $this->assertInstanceOf(Some::class, $mapped);
        $this->assertSame([null], $mapped->getOrElse('none'));
    }

    public function testFlatMapOnSomeHoldingNullPassesNullToTheFunction(): void
    {
        $result = (new Some(null))->flatMap(static fn (mixed ...$arguments): Option => new Some($arguments));

        $this->assertInstanceOf(Some::class, $result);
        $this->assertSame([null], $result->getOrElse('none'));
    }

    public function testMapOnSomeCallsTheFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Some(42))->map(static function (int $value) use (&$calls): int {
            $calls++;

            return $value;
        });

        $this->assertSame(1, $calls);
    }

    public function testFlatMapOnSomeCallsTheFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Some(42))->flatMap(static function (int $value) use (&$calls): Option {
            $calls++;

            return new Some($value);
        });

        $this->assertSame(1, $calls);
    }
}
