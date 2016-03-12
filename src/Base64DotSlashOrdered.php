<?php
namespace ParagonIE\ConstantTime;

abstract class Base64DotSlashOrdered extends Base64
{
    /**
     * Base64 character set:
     * [A-Z]      [a-z]      [0-9]      +     /
     * 0x41-0x5a, 0x61-0x7a, 0x30-0x39, 0x2b, 0x2f
     *
     * @param $src
     * @return int
     */
    protected static function decode6Bits($src)
    {
        $ret = -1;

        // if ($src > 0x2d && $src < 0x3a) ret += $src - 0x2e + 1; // -45
        $ret += (((0x2d - $src) & ($src - 0x3a)) >> 8) & ($src - 45);

        // if ($src > 0x40 && $src < 0x5b) ret += $src - 0x41 + 12 + 1; // -52
        $ret += (((0x40 - $src) & ($src - 0x5b)) >> 8) & ($src - 52);

        // if ($src > 0x60 && $src < 0x7b) ret += $src - 0x61 + 38 + 1; // -58
        $ret += (((0x60 - $src) & ($src - 0x7b)) >> 8) & ($src - 58);

        return $ret;
    }

    /**
     * @param $src
     * @return string
     */
    protected static function encode6Bits($src)
    {
        $src += 0x2e;

        // if ($src > 0x39) $src += 0x41 - 0x3a; // 7
        $src += ((0x39 - $src) >> 8) & 7;

        // if ($src > 0x5a) $src += 0x61 - 0x5b; // 6
        $src += ((0x5a - $src) >> 8) & 6;

        return \pack('C', $src);
    }
}
