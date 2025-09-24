<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base32;
use RangeException;
use function random_bytes;
use function rtrim;

class Base32Test extends TestCase
{
    public function testRandom(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base32::encode($random);
                $this->assertSame(
                    $random,
                    Base32::decode($enc)
                );
                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $unpadded,
                    Base32::encodeUnpadded($random)
                );
                $this->assertSame(
                    $random,
                    Base32::decode($unpadded)
                );

                $enc = Base32::encodeUpper($random);
                $this->assertSame(
                    $random,
                    Base32::decodeUpper($enc)
                );
                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $unpadded,
                    Base32::encodeUpperUnpadded($random)
                );
                $this->assertSame(
                    $random,
                    Base32::decodeUpper($unpadded)
                );
            }
        }
    }

    public static function canonProvider(): array
    {
        return [
            ['me', 'mf'],
            ['mfra', 'mfrb'],
            ['mfrgg', 'mfrgh'],
            ['mfrggza', 'mfrggzb']
        ];
    }

    /**
     * We need this for PHP before attributes
     * @dataProvider canonProvider
     */
    #[DataProvider("canonProvider")]
    public function testCanonicalBase32(string $canonical, string $munged)
    {
        Base32::decode($canonical);
        $this->expectException(RangeException::class);
        Base32::decodeNoPadding($munged);
    }

    public function testDecodeNoPadding()
    {
        // Empty string
        $this->assertSame('', Base32::decodeNoPadding(''));

        // Valid unpadded strings
        $this->assertSame('f', Base32::decodeNoPadding('my'));
        $this->assertSame('fo', Base32::decodeNoPadding('mzxq'));
        $this->assertSame('foo', Base32::decodeNoPadding('mzxw6'));
        $this->assertSame('foob', Base32::decodeNoPadding('mzxw6yq'));
        $this->assertSame('fooba', Base32::decodeNoPadding('mzxw6ytb'));
        $this->assertSame('foobar', Base32::decodeNoPadding('mzxw6ytboi'));

        // Valid unpadded strings (uppercase)
        $this->assertSame('f', Base32::decodeNoPadding('MY', true));
        $this->assertSame('fo', Base32::decodeNoPadding('MZXQ', true));
        $this->assertSame('foo', Base32::decodeNoPadding('MZXW6', true));
        $this->assertSame('foob', Base32::decodeNoPadding('MZXW6YQ', true));
        $this->assertSame('fooba', Base32::decodeNoPadding('MZXW6YTB', true));
        $this->assertSame('foobar', Base32::decodeNoPadding('MZXW6YTBOI', true));
    }

    public function testDecodeNoPaddingWithPadding()
    {
        $this->expectException(InvalidArgumentException::class);
        Base32::decodeNoPadding('MZXW6YTB========');
    }

    public function testDecodeNoPaddingWithPaddingUpper()
    {
        $this->expectException(InvalidArgumentException::class);
        Base32::decodeNoPadding('MZXW6YTB========', true);
    }

    public function testDecodeNoPaddingInvalidLength()
    {
        $this->expectException(RangeException::class);
        Base32::decodeNoPadding('M');
    }

    public function testNonCanonicalPadding()
    {
        // These should be accepted with default (non-strict) padding
        $this->assertSame('f', Base32::decode('my======='));
        $this->assertSame('f', Base32::decodeUpper('MY======='));
        $this->assertSame('fo', Base32::decode('mzxq====='));
        $this->assertSame('fo', Base32::decodeUpper('MZXQ====='));
        $this->assertSame('foo', Base32::decode('mzxw6===='));
        $this->assertSame('foo', Base32::decodeUpper('MZXW6===='));
        $this->assertSame('foob', Base32::decode('mzxw6yq=='));
        $this->assertSame('foob', Base32::decodeUpper('MZXW6YQ=='));
        $this->assertSame('foobar', Base32::decode('mzxw6ytboi======='));
        $this->assertSame('foobar', Base32::decodeUpper('MZXW6YTBOI======='));
    }

    public static function invalidStrictPaddingProvider(): array
    {
        return [
            ['MY======='],
            ['MZXQ====='],
            ['MZXW6===='],
            ['MZXW6YQ=='],
            ['MZXW6YTBOI======='],
            ['M'], // Incorrect length
            ['ME='] // Incorrect padding
        ];
    }

    /**
     * @dataProvider invalidStrictPaddingProvider
     */
    #[DataProvider("invalidStrictPaddingProvider")]
    public function testInvalidStrictPadding(string $encoded): void
    {
        $this->expectException(RangeException::class);
        Base32::decode($encoded, true);
    }

    /**
     * @dataProvider invalidStrictPaddingProvider
     */
    #[DataProvider("invalidStrictPaddingProvider")]
    public function testInvalidStrictPaddingUpper(string $encoded): void
    {
        $this->expectException(RangeException::class);
        Base32::decodeUpper($encoded, true);
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['a`a'],
            ['a{a'],
            ['a1a'],
            ['a8a'],
            ['A@A'],
            ['A[A'],
            ['A1A'],
            ['A8A'],
        ];
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded): void
    {
        $this->expectException(RangeException::class);
        Base32::decode($encoded);
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharactersUpper(string $encoded): void
    {
        $this->expectException(RangeException::class);
        Base32::decodeUpper(strtoupper($encoded));
    }

    public function testSingleInvalidCharacter(): void
    {
        $this->expectException(RangeException::class);
        Base32::decode('aaaaaaaa`aaaaaaa');
    }

    public function testInvalidPaddingBits(): void
    {
        $this->expectException(RangeException::class);
        // Last 3 bits of last char should be 0
        Base32::decode('999999J=', true);
    }
}
