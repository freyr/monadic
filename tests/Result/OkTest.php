<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Result;

use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ok::class)]
final class OkTest extends TestCase
{
    public function testGetOnOkReturnsTheHeldValue(): void
    {
        $this->assertSame(42, (new Ok(42))->get());
    }

    public function testMapOnOkReturnsAnOkThatHoldsTheReturnedValue(): void
    {
        $mapped = (new Ok(42))->map(static fn (int $value): int => $value + 1);

        $this->assertInstanceOf(Ok::class, $mapped);
        $this->assertSame(43, $mapped->get());
    }

    public function testMapOnOkPassesExactlyTheHeldValueToTheFunction(): void
    {
        $mapped = (new Ok(42))->map(static fn (mixed ...$arguments): array => $arguments);

        $this->assertInstanceOf(Ok::class, $mapped);
        $this->assertSame([42], $mapped->get());
    }

    public function testFlatMapOnOkReturnsExactlyTheOkThatTheFunctionReturns(): void
    {
        $returned = new Ok('inner');

        $this->assertSame($returned, (new Ok(42))->flatMap(static fn (int $value): Result => $returned));
    }

    public function testFlatMapOnOkReturnsExactlyTheErrThatTheFunctionReturns(): void
    {
        $returned = new Err('failure');

        $this->assertSame($returned, (new Ok(42))->flatMap(static fn (int $value): Result => $returned));
    }

    public function testFlatMapOnOkPassesExactlyTheHeldValueToTheFunction(): void
    {
        $result = (new Ok(42))->flatMap(static fn (mixed ...$arguments): Ok => new Ok($arguments));

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertSame([42], $result->get());
    }

    public function testFoldOnOkReturnsTheResultOfTheOkFunctionCalledWithTheValue(): void
    {
        $folded = (new Ok(42))->fold(
            static fn (mixed ...$arguments): array => ['ok', $arguments],
            static fn (mixed ...$arguments): array => ['err', $arguments],
        );

        $this->assertSame(['ok', [42]], $folded);
    }

    public function testFoldOnOkHoldingNullPassesNullToTheOkFunction(): void
    {
        $folded = (new Ok(null))->fold(
            static fn (mixed ...$arguments): array => ['ok', $arguments],
            static fn (mixed ...$arguments): array => ['err', $arguments],
        );

        $this->assertSame(['ok', [null]], $folded);
    }

    public function testFoldOnOkDoesNotCallTheErrFunction(): void
    {
        $err = static function (): never {
            self::fail('fold() on Ok must not call the err function.');
        };

        $this->assertSame('ok', (new Ok(42))->fold(static fn (int $value): string => 'ok', $err));
    }

    public function testMapOnOkToNullReturnsAnOkThatHoldsNull(): void
    {
        $mapped = (new Ok(42))->map(static fn (int $value): null => null);

        $this->assertInstanceOf(Ok::class, $mapped);
        $this->assertNull($mapped->get());
    }

    public function testMapOnOkToAnErrReturnsAnOkThatHoldsThatErr(): void
    {
        $inner = new Err('inner failure');
        $mapped = (new Ok(42))->map(static fn (int $value): Err => $inner);

        $this->assertInstanceOf(Ok::class, $mapped);
        $this->assertSame($inner, $mapped->get());
    }

    public function testMapOnOkHoldingNullPassesNullToTheFunction(): void
    {
        $mapped = (new Ok(null))->map(static fn (mixed ...$arguments): array => $arguments);

        $this->assertInstanceOf(Ok::class, $mapped);
        $this->assertSame([null], $mapped->get());
    }

    public function testFlatMapOnOkHoldingNullPassesNullToTheFunction(): void
    {
        $result = (new Ok(null))->flatMap(static fn (mixed ...$arguments): Ok => new Ok($arguments));

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertSame([null], $result->get());
    }

    public function testGetOnOkHoldingZeroReturnsZero(): void
    {
        $this->assertSame(0, (new Ok(0))->get());
    }

    public function testMapOnOkCallsTheFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Ok(42))->map(static function (int $value) use (&$calls): int {
            $calls++;

            return $value;
        });

        $this->assertSame(1, $calls);
    }

    public function testFlatMapOnOkCallsTheFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Ok(42))->flatMap(static function (int $value) use (&$calls): Ok {
            $calls++;

            return new Ok($value);
        });

        $this->assertSame(1, $calls);
    }

    public function testFoldOnOkCallsTheOkFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Ok(42))->fold(
            static function (int $value) use (&$calls): string {
                $calls++;

                return 'ok';
            },
            static fn (mixed $error): string => 'err',
        );

        $this->assertSame(1, $calls);
    }
}
