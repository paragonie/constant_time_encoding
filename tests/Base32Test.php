<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base32;

class Base32Test extends TestCase
{
    public function testRandom(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Base32::encode($random);
                $this->assertSame(
                    $random,
                    Base32::decode($enc)
                );
                $unpadded = \rtrim($enc, '=');
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
                $unpadded = \rtrim($enc, '=');
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
        $this->expectException(\RangeException::class);
        Base32::decodeNoPadding($munged);
    }

    public function testDecodeNoPadding()
    {
        Base32::decodeNoPadding('aaaqe');
        $this->expectException(InvalidArgumentException::class);
        Base32::decodeNoPadding('aaaqe===');
    }
}
