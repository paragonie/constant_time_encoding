<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base64UrlSafe;
use ParagonIE\ConstantTime\Binary;
use RangeException;
use TypeError;

/**
 * Class Base64UrlSafeTest
 */
class Base64UrlSafeTest extends TestCase
{
    use CanonicalTrait;

    /**
     * @covers Base64UrlSafe::encode()
     * @covers Base64UrlSafe::decode()
     *
     * @throws Exception
     * @throws TypeError
     */
    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Base64UrlSafe::encode($random);
                $this->assertSame(
                    $random,
                    Base64UrlSafe::decode($enc)
                );
                $this->assertSame(
                    \strtr(\base64_encode($random), '+/', '-_'),
                    $enc
                );

                $unpadded = \rtrim($enc, '=');
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

        $random = \random_bytes(1 << 20);
        $enc = Base64UrlSafe::encode($random);
        $this->assertTrue(Binary::safeStrlen($enc) > 65536);
        $this->assertSame(
            $random,
            Base64UrlSafe::decode($enc)
        );
        $this->assertSame(
            \strtr(\base64_encode($random), '+/', '-_'),
            $enc
        );
    }

    public function testDecodeNoPadding()
    {
        Base64UrlSafe::decodeNoPadding('0w');
        $this->expectException(InvalidArgumentException::class);
        Base64UrlSafe::decodeNoPadding('0w==');
    }

    /**
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
}
