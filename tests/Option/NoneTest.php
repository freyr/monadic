<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Option;

use Freyr\Monadic\Option\None;
use PHPUnit\Framework\TestCase;

final class NoneTest extends TestCase
{
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
}
