<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Result;

use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Err::class)]
final class ErrTest extends TestCase
{
    public function testGetErrorOnErrReturnsTheHeldError(): void
    {
        $this->assertSame('failure', (new Err('failure'))->getError());
    }

    public function testMapOnErrReturnsAnErrThatKeepsTheSameError(): void
    {
        $mapped = (new Err('failure'))->map(static fn (mixed $value): string => 'mapped');

        $this->assertInstanceOf(Err::class, $mapped);
        $this->assertSame('failure', $mapped->getError());
    }

    public function testMapOnErrDoesNotCallTheFunction(): void
    {
        $function = static function (): never {
            self::fail('map() on Err must not call the function.');
        };

        $this->assertInstanceOf(Err::class, (new Err('failure'))->map($function));
    }

    public function testFlatMapOnErrReturnsAnErrThatKeepsTheSameError(): void
    {
        $result = (new Err('failure'))->flatMap(static fn (mixed $value): Ok => new Ok('inner'));

        $this->assertInstanceOf(Err::class, $result);
        $this->assertSame('failure', $result->getError());
    }

    public function testFlatMapOnErrDoesNotCallTheFunction(): void
    {
        $this->assertInstanceOf(Err::class, (new Err('failure'))->flatMap(self::flatMapFunctionThatMustNotBeCalled(...)));
    }

    /**
     * @return Result<string, string>
     */
    private static function flatMapFunctionThatMustNotBeCalled(): Result
    {
        self::fail('flatMap() on Err must not call the function.');
    }

    public function testFoldOnErrReturnsTheResultOfTheErrFunctionCalledWithTheError(): void
    {
        $folded = (new Err('failure'))->fold(
            static fn (mixed ...$arguments): array => ['ok', $arguments],
            static fn (mixed ...$arguments): array => ['err', $arguments],
        );

        $this->assertSame(['err', ['failure']], $folded);
    }

    public function testFoldOnErrDoesNotCallTheOkFunction(): void
    {
        $ok = static function (): never {
            self::fail('fold() on Err must not call the ok function.');
        };

        $this->assertSame('err', (new Err('failure'))->fold($ok, static fn (string $error): string => 'err'));
    }

    public function testFoldOnErrHoldingNullPassesNullToTheErrFunction(): void
    {
        $folded = (new Err(null))->fold(
            static fn (mixed ...$arguments): array => ['ok', $arguments],
            static fn (mixed ...$arguments): array => ['err', $arguments],
        );

        $this->assertSame(['err', [null]], $folded);
    }

    public function testMapOnErrHoldingZeroReturnsAnErrThatKeepsZero(): void
    {
        $mapped = (new Err(0))->map(static fn (mixed $value): string => 'mapped');

        $this->assertInstanceOf(Err::class, $mapped);
        $this->assertSame(0, $mapped->getError());
    }

    public function testFlatMapOnErrHoldingZeroReturnsAnErrThatKeepsZero(): void
    {
        $result = (new Err(0))->flatMap(self::flatMapFunctionThatMustNotBeCalled(...));

        $this->assertInstanceOf(Err::class, $result);
        $this->assertSame(0, $result->getError());
    }

    public function testFoldOnErrCallsTheErrFunctionExactlyOnce(): void
    {
        $calls = 0;
        (new Err('failure'))->fold(
            static fn (mixed $value): string => 'ok',
            static function (string $error) use (&$calls): string {
                $calls++;

                return 'err';
            },
        );

        $this->assertSame(1, $calls);
    }
}
