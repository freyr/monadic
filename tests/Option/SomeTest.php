<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Some::class)]
final class SomeTest extends TestCase
{
    public function testIsDefined(): void
    {
        $some = new Some(42);
        $this->assertTrue($some->isDefined());
    }

    public function testMapAppliesFunction(): void
    {
        $some = new Some(42);
        $result = $some->map(fn ($x) => $x + 1);
        $this->assertEquals(43, $result->getOrElse(0));
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
}
