<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option\Some;
use PHPUnit\Framework\TestCase;

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
}
