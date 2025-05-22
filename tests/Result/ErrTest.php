<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Result;

use Freyr\Monadic\Result\Err;
use PHPUnit\Framework\TestCase;

final class ErrTest extends TestCase
{
    public function testGetError(): void
    {
        $err = new Err('error');
        $this->assertEquals('error', $err->getError());
    }

    public function testMapReturnsErr(): void
    {
        $err = new Err('error');
        $this->assertInstanceOf(Err::class, $err->map(fn ($x) => $x));
    }
}
