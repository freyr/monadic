<?php

declare(strict_types=1);

namespace Freyr\Monadic\Tests\Result;

use Freyr\Monadic\Result\Ok;
use PHPUnit\Framework\TestCase;

final class OkTest extends TestCase
{
    public function testGet(): void
    {
        $ok = new Ok(42);
        $this->assertEquals(42, $ok->get());
    }

    public function testMapAppliesFunction(): void
    {
        $ok = new Ok(42);
        $result = $ok->map(fn ($x) => $x + 1);
        $this->assertEquals(43, $result->get());
    }
}
