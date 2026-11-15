<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option;
use Freyr\Monadic\Option\None;
use Freyr\Monadic\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit is `new Some($x)`. Each side of a law is read with getOrElse('none'),
 * so 'none' stands for a None.
 */
#[CoversClass(Some::class)]
#[CoversClass(None::class)]
final class OptionMonadLawsTest extends TestCase
{
    /**
     * @return iterable<string, array{int, callable(int): Option<int>, int|string}>
     */
    public static function leftIdentityCases(): iterable
    {
        yield 'function returns a Some' => [
            3,
            static fn (int $value): Option => new Some($value * 2),
            6,
        ];
        yield 'function returns a Some of zero' => [
            0,
            static fn (int $value): Option => new Some($value),
            0,
        ];
        yield 'function returns a None' => [
            3,
            static fn (int $value): Option => new None(),
            'none',
        ];
    }

    /**
     * @param callable(int): Option<int> $f
     */
    #[DataProvider('leftIdentityCases')]
    public function testLeftIdentityUnitThenFlatMapEqualsTheFunctionApplied(
        int $value,
        callable $f,
        int|string $expected
    ): void {
        $left = (new Some($value))->flatMap($f);
        $right = $f($value);

        $this->assertSame($expected, $left->getOrElse('none'));
        $this->assertSame($expected, $right->getOrElse('none'));
    }

    /**
     * @return iterable<string, array{Option<int>, int|string}>
     */
    public static function rightIdentityCases(): iterable
    {
        yield 'a Some' => [new Some(5), 5];
        yield 'a Some of zero' => [new Some(0), 0];
        yield 'a None' => [new None(), 'none'];
    }

    /**
     * @param Option<int> $option
     */
    #[DataProvider('rightIdentityCases')]
    public function testRightIdentityFlatMapWithUnitEqualsTheOriginal(Option $option, int|string $expected): void
    {
        $left = $option->flatMap(static fn (int $value): Option => new Some($value));

        $this->assertSame($expected, $left->getOrElse('none'));
        $this->assertSame($expected, $option->getOrElse('none'));
    }

    /**
     * @return iterable<string, array{Option<int>, callable(int): Option<int>, callable(int): Option<int>, int|string}>
     */
    public static function associativityCases(): iterable
    {
        $double = static fn (int $value): Option => new Some($value * 2);
        $increment = static fn (int $value): Option => new Some($value + 1);
        $toNone = static fn (int $value): Option => new None();

        yield 'both functions return a Some' => [new Some(3), $double, $increment, 7];
        yield 'first function returns a None' => [new Some(3), $toNone, $increment, 'none'];
        yield 'second function returns a None' => [new Some(3), $double, $toNone, 'none'];
        yield 'starts from a None' => [new None(), $double, $increment, 'none'];
    }

    /**
     * @param Option<int> $option
     * @param callable(int): Option<int> $f
     * @param callable(int): Option<int> $g
     */
    #[DataProvider('associativityCases')]
    public function testAssociativityTheGroupingOfFlatMapsDoesNotChangeTheResult(
        Option $option,
        callable $f,
        callable $g,
        int|string $expected
    ): void {
        $left = $option->flatMap($f)->flatMap($g);
        $right = $option->flatMap(static fn (int $value): Option => $f($value)->flatMap($g));

        $this->assertSame($expected, $left->getOrElse('none'));
        $this->assertSame($expected, $right->getOrElse('none'));
    }
}
