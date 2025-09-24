<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base64UrlSafe;
use RangeException;
use TypeError;
use function base64_encode;
use function bin2hex;
use function random_bytes;
use function rtrim;
use function strlen;
use function strtr;

class Base64UrlSafeTest extends TestCase
{
    use CanonicalTrait;

    /**
     * @throws Exception
     * @throws TypeError
     */
    public function testRandom(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base64UrlSafe::encode($random);
                $this->assertSame(
                    $random,
                    Base64UrlSafe::decode($enc)
                );
                $this->assertSame(
                    strtr(base64_encode($random), '+/', '-_'),
                    $enc
                );

                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $unpadded,
                    Base64UrlSafe::encodeUnpadded($random)
                );
                $this->assertSame(
                    $random,
                    Base64UrlSafe::decode($unpadded)
                );
            }
        }

        $random = random_bytes(1 << 20);
        $enc = Base64UrlSafe::encode($random);
        $this->assertTrue(strlen($enc) > 65536);
        $this->assertSame(
            $random,
            Base64UrlSafe::decode($enc)
        );
        $this->assertSame(
            strtr(base64_encode($random), '+/', '-_'),
            $enc
        );
    }

    /**
     * @throws Exception
     */
    public function testUnpadded(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64UrlSafe::encodeUnpadded($random);
            $decoded = Base64UrlSafe::decodeNoPadding($encoded);
            $this->assertSame(
                bin2hex($random),
                bin2hex($decoded)
            );
        }
    }

    #[DataProvider("canonicalDataProvider")]
    /**
     * We need this for PHP before attributes
     * @dataProvider canonicalDataProvider
     */
    public function testNonCanonical(string $input)
    {
        $w = Base64UrlSafe::encodeUnpadded($input);
        Base64UrlSafe::decode($w);
        Base64UrlSafe::decode($w, true);

        // Mess with padding:
        $x = $this->increment($w);
        Base64UrlSafe::decode($x);

        // Should throw in strict mode:
        $this->expectException(RangeException::class);
        Base64UrlSafe::decode($x, true);
    }

    protected function getNextChar(string $c): string
    {
        return strtr(
            $c,
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_',
            'BCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_A'
        );
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['ab+d'],
            ['ab/d'],
            ['ab.d'],
            ['ab:d'],
            ['ab[d'],
        ];
    }

    /**
     * Detect issue underlying #67 with ext-sodium
     *
     * @return void
     * @throws Exception
     */
    public function testStrictPaddingSodium(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64UrlSafe::encode($random);
            $decoded = Base64UrlSafe::decode($encoded, true);
            $this->assertSame($decoded, $random);
        }
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded)
    {
        $this->expectException(RangeException::class);
        Base64UrlSafe::decode($encoded);
    }
}
