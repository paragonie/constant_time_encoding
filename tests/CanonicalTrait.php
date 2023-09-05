<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use ParagonIE\ConstantTime\Binary;

trait CanonicalTrait
{
    public static function canonicalDataProvider(): array
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

    abstract protected function getNextChar(string $c): string;

    protected function increment(string $str): string
    {
        $i = Binary::safeStrlen($str) - 1;
        $c = $this->getNextChar($str[$i]);
        return Binary::safeSubstr($str, 0, $i) . $c;
    }
}
