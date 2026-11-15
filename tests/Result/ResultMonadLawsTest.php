<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Result;

use Freyr\Monadic\Result;
use Freyr\Monadic\Result\Err;
use Freyr\Monadic\Result\Ok;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit is `new Ok($x)`. Each side of a law is read with fold() into
 * 'ok:<value>' or 'err:<error>'.
 */
#[CoversClass(Ok::class)]
#[CoversClass(Err::class)]
final class ResultMonadLawsTest extends TestCase
{
    /**
     * @return iterable<string, array{int, callable(int): Result<int, string>, string}>
     */
    public static function leftIdentityCases(): iterable
    {
        yield 'function returns an Ok' => [
            3,
            static fn (int $value): Result => new Ok($value * 2),
            'ok:6',
        ];
        yield 'function returns an Ok of zero' => [
            0,
            static fn (int $value): Result => new Ok($value),
            'ok:0',
        ];
        yield 'function returns an Err' => [
            3,
            static fn (int $value): Result => new Err('rejected'),
            'err:rejected',
        ];
    }

    /**
     * @param callable(int): Result<int, string> $f
     */
    #[DataProvider('leftIdentityCases')]
    public function testLeftIdentityUnitThenFlatMapEqualsTheFunctionApplied(
        int $value,
        callable $f,
        string $expected
    ): void {
        $left = (new Ok($value))->flatMap($f);
        $right = $f($value);

        $this->assertSame($expected, self::describe($left));
        $this->assertSame($expected, self::describe($right));
    }

    /**
     * @return iterable<string, array{Result<int, string>, string}>
     */
    public static function rightIdentityCases(): iterable
    {
        yield 'an Ok' => [new Ok(5), 'ok:5'];
        yield 'an Ok of zero' => [new Ok(0), 'ok:0'];
        yield 'an Err' => [new Err('rejected'), 'err:rejected'];
    }

    /**
     * @param Result<int, string> $result
     */
    #[DataProvider('rightIdentityCases')]
    public function testRightIdentityFlatMapWithUnitEqualsTheOriginal(Result $result, string $expected): void
    {
        $left = $result->flatMap(static fn (int $value): Result => new Ok($value));

        $this->assertSame($expected, self::describe($left));
        $this->assertSame($expected, self::describe($result));
    }

    /**
     * @return iterable<string, array{Result<int, string>, callable(int): Result<int, string>, callable(int): Result<int, string>, string}>
     */
    public static function associativityCases(): iterable
    {
        $double = static fn (int $value): Result => new Ok($value * 2);
        $increment = static fn (int $value): Result => new Ok($value + 1);
        $rejectFirst = static fn (int $value): Result => new Err('first');
        $rejectSecond = static fn (int $value): Result => new Err('second');

        yield 'both functions return an Ok' => [new Ok(3), $double, $increment, 'ok:7'];
        yield 'first function returns an Err' => [new Ok(3), $rejectFirst, $increment, 'err:first'];
        yield 'second function returns an Err' => [new Ok(3), $double, $rejectSecond, 'err:second'];
        yield 'starts from an Err' => [new Err('initial'), $double, $increment, 'err:initial'];
    }

    /**
     * @param Result<int, string> $result
     * @param callable(int): Result<int, string> $f
     * @param callable(int): Result<int, string> $g
     */
    #[DataProvider('associativityCases')]
    public function testAssociativityTheGroupingOfFlatMapsDoesNotChangeTheResult(
        Result $result,
        callable $f,
        callable $g,
        string $expected
    ): void {
        $left = $result->flatMap($f)->flatMap($g);
        $right = $result->flatMap(static fn (int $value): Result => $f($value)->flatMap($g));

        $this->assertSame($expected, self::describe($left));
        $this->assertSame($expected, self::describe($right));
    }

    /**
     * @param Result<int, string> $result
     */
    private static function describe(Result $result): string
    {
        return $result->fold(
            static fn (int $value): string => 'ok:' . $value,
            static fn (string $error): string => 'err:' . $error,
        );
    }
}
