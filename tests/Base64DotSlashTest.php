<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use InvalidArgumentException;
use ParagonIE\ConstantTime\Base64DotSlash;
use PHPUnit\Framework\TestCase;
use RangeException;

class Base64DotSlashTest extends TestCase
{
    use CanonicalTrait;

    /**
     * @covers Base64DotSlash::encode()
     * @covers Base64DotSlash::decode()
     */
    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Base64DotSlash::encode($random);
                $this->assertSame(
                    $random,
                    Base64DotSlash::decode($enc)
                );

                $unpadded = \rtrim($enc, '=');
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

    public function testDecodeNoPadding()
    {
        Base64DotSlash::decodeNoPadding('..');
        $this->expectException(InvalidArgumentException::class);
        Base64DotSlash::decodeNoPadding('..==');
    }

    /**
     * @dataProvider canonicalDataProvider
     */
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
}
