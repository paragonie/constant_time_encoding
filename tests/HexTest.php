<?php
namespace ParagonIE\ConstantTime\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Hex;
use function bin2hex;
use function strtoupper;

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
                    bin2hex($random),
                    $enc
                );

                $enc = Hex::encodeUpper($random);
                $this->assertSame(
                    $random,
                    Hex::decode($enc)
                );
                $this->assertSame(
                    strtoupper(bin2hex($random)),
                    $enc
                );
            }
        }
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['gg'],
            ['GG'],
            ['zz'],
            ['ZZ'],
            ['  '],
            ['ab de'],
        ];
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded)
    {
        $this->expectException(\RangeException::class);
        Hex::decode($encoded);
    }

    public function testStrictPaddingSuccess(): void
    {
        Hex::decode('0a', true);
        $this->assertTrue(true); // To avoid risky test warning
    }

    public function testStrictPaddingFailure(): void
    {
        $this->expectException(\RangeException::class);
        Hex::decode('a', true);
    }

    public function testNonStrictPadding(): void
    {
        // Odd-length string with non-strict padding should be prepended with 0
        $this->assertSame(
            Hex::decode('0a'),
            Hex::decode('a')
        );
    }
}
