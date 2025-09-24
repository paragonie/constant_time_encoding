<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use InvalidArgumentException;
use ParagonIE\ConstantTime\Base32Hex;
use ParagonIE\ConstantTime\Binary;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RangeException;
use function random_bytes;
use function rtrim;

class Base32HexTest extends TestCase
{
    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base32Hex::encode($random);
                $this->assertSame(
                    $random,
                    Base32Hex::decode($enc)
                );
                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $unpadded,
                    Base32Hex::encodeUnpadded($random)
                );
                $this->assertSame(
                    $random,
                    Base32Hex::decode($unpadded)
                );

                $enc = Base32Hex::encodeUpper($random);
                $this->assertSame(
                    $random,
                    Base32Hex::decodeUpper($enc)
                );                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $unpadded,
                    Base32Hex::encodeUpperUnpadded($random)
                );
                $this->assertSame(
                    $random,
                    Base32Hex::decodeUpper($unpadded)
                );
            }
        }
    }

    public function testUnpadded()
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base32Hex::encodeUnpadded($random);
            $decoded = Base32Hex::decodeNoPadding($encoded);
            $this->assertSame(
                Binary::safeStrlen($random),
                Binary::safeStrlen($decoded),
                'decoded strlen mismatch'
            );
            $this->assertSame(
                bin2hex($random),
                bin2hex($decoded),
                'decoded hex mismatch'
            );
        }
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['a/a'],
            ['a:a'],
            ['a`a'],
            ['awa'],
            ['A/A'],
            ['A:A'],
            ['A@A'],
            ['AWA'],
        ];
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded)
    {
        $this->expectException(RangeException::class);
        Base32Hex::decode($encoded);
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharactersUpper(string $encoded)
    {
        $this->expectException(RangeException::class);
        Base32Hex::decodeUpper(strtoupper($encoded));
    }
}
