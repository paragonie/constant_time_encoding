<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use ParagonIE\ConstantTime\Binary;

/**
 * @method getNextChar(string $c): string
 */
trait CanonicalTrait
{
    public function canonicalDataProvider(): array
    {
        return [
            ['a'],
            ['ab'],
            ['abcd'],
            ["\xff"],
            ["\xff\xff"],
            ["\xff\xff\xff\xff"]
        ];
    }

    protected function increment(string $str): string
    {
        $i = Binary::safeStrlen($str) - 1;
        $c = $this->getNextChar($str[$i]);
        return Binary::safeSubstr($str, 0, $i) . $c;
    }
}
