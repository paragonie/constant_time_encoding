<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base64DotSlashOrdered;
use RangeException;

class Base64DotSlashOrderedTest extends TestCase
{
    use CanonicalTrait;

    /**
     * @covers Base64DotSlashOrdered::encode()
     * @covers Base64DotSlashOrdered::decode()
     */
    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Base64DotSlashOrdered::encode($random);
                $this->assertSame(
                    $random,
                    Base64DotSlashOrdered::decode($enc)
                );

                $unpadded = \rtrim($enc, '=');
                $this->assertSame(
                    $random,
                    Base64DotSlashOrdered::decode($unpadded)
                );
                $this->assertSame(
                    $random,
                    Base64DotSlashOrdered::decode($unpadded)
                );
            }
        }
    }

    public function testDecodeNoPadding()
    {
        Base64DotSlashOrdered::decodeNoPadding('..');
        $this->expectException(InvalidArgumentException::class);
        Base64DotSlashOrdered::decodeNoPadding('..==');
    }

    /**
     * @dataProvider canonicalDataProvider
     */
    public function testNonCanonical(string $input)
    {
        $w = Base64DotSlashOrdered::encodeUnpadded($input);
        Base64DotSlashOrdered::decode($w);
        Base64DotSlashOrdered::decode($w, true);

        // Mess with padding:
        $x = $this->increment($w);
        Base64DotSlashOrdered::decode($x);

        // Should throw in strict mode:
        $this->expectException(RangeException::class);
        Base64DotSlashOrdered::decode($x, true);
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
