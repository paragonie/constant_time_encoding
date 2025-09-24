<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base64DotSlashOrdered;
use RangeException;
use function bin2hex;
use function random_bytes;
use function rtrim;

class Base64DotSlashOrderedTest extends TestCase
{
    use CanonicalTrait;

    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base64DotSlashOrdered::encode($random);
                $this->assertSame(
                    $random,
                    Base64DotSlashOrdered::decode($enc)
                );

                $unpadded = rtrim($enc, '=');
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

    public function testUnpadded()
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64DotSlashOrdered::encodeUnpadded($random);
            $decoded = Base64DotSlashOrdered::decodeNoPadding($encoded);
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
        Base64DotSlashOrdered::decode($encoded);
    }
}
