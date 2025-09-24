<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use ParagonIE\ConstantTime\Base64DotSlash;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RangeException;
use function bin2hex;
use function random_bytes;
use function rtrim;

class Base64DotSlashTest extends TestCase
{
    use CanonicalTrait;

    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base64DotSlash::encode($random);
                $this->assertSame(
                    $random,
                    Base64DotSlash::decode($enc)
                );

                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $random,
                    Base64DotSlash::decode($unpadded)
                );
                $this->assertSame(
                    $random,
                    Base64DotSlash::decode($unpadded)
                );
            }
        }
    }

    public function testUnpadded()
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64DotSlash::encodeUnpadded($random);
            $decoded = Base64DotSlash::decodeNoPadding($encoded);
            $this->assertSame(
                bin2hex($random),
                bin2hex($decoded)
            );
        }
    }

    /**
     * We need this for PHP before attributes
     * @dataProvider canonicalDataProvider
     */
    #[DataProvider("canonicalDataProvider")]
    public function testNonCanonical(string $input)
    {
        $w = Base64DotSlash::encodeUnpadded($input);
        Base64DotSlash::decode($w);
        Base64DotSlash::decode($w, true);

        // Mess with padding:
        $x = $this->increment($w);
        Base64DotSlash::decode($x);

        // Should throw in strict mode:
        $this->expectException(RangeException::class);
        Base64DotSlash::decode($x, true);
    }

    protected function getNextChar(string $c): string
    {
        return strtr(
            $c,
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./',
            'BCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./A'
        );
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['ab-d'],
            ['ab d'],
            ['ab[d'],
        ];
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded)
    {
        $this->expectException(RangeException::class);
        Base64DotSlash::decode($encoded);
    }
}
