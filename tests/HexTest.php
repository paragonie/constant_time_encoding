<?php
namespace ParagonIE\ConstantTime\Tests;

use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Hex;

class HexTest extends TestCase
{
    public function testRandom(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Hex::encode($random);
                $this->assertSame(
                    $random,
                    Hex::decode($enc)
                );
                $this->assertSame(
                    \bin2hex($random),
                    $enc
                );

                $enc = Hex::encodeUpper($random);
                $this->assertSame(
                    $random,
                    Hex::decode($enc)
                );
                $this->assertSame(
                    \strtoupper(\bin2hex($random)),
                    $enc
                );
            }
        }
    }
}
