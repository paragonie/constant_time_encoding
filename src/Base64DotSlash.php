<?php
namespace ParagonIE\ConstantTime;

/**
 * Class Base64DotSlash
 * ./[A-Z][a-z][0-9]
 *
 * @package ParagonIE\ConstantTime
 */
abstract class Base64DotSlash extends Base64
{
    /**
     *
     * Base64 character set:
     * ./         [A-Z]      [a-z]     [0-9]
     * 0x2e-0x2f, 0x41-0x5a, 0x61-0x7a, 0x30-0x39
     *
     * @param int $src
     * @return int
     */
    protected static function decode6Bits($src)
    {
        $ret = -1;

        // if ($src > 0x2d && $src < 0x30) ret += $src - 0x2e + 1; // -45
        $ret += (((0x2d - $src) & ($src - 0x30)) >> 8) & ($src - 45);

        // if ($src > 0x40 && $src < 0x5b) ret += $src - 0x41 + 2 + 1; // -62
        $ret += (((0x40 - $src) & ($src - 0x5b)) >> 8) & ($src - 62);

        // if ($src > 0x60 && $src < 0x7b) ret += $src - 0x61 + 28 + 1; // -68
        $ret += (((0x60 - $src) & ($src - 0x7b)) >> 8) & ($src - 68);

        // if ($src > 0x2f && $src < 0x3a) ret += $src - 0x30 + 54 + 1; // 7
        $ret += (((0x2f - $src) & ($src - 0x3a)) >> 8) & ($src + 7);

        return $ret;
    }

    /**
     * @param int $src
     * @return string
     */
    protected static function encode6Bits($src)
    {
        $src += 0x2e;

        // if ($src > 0x2f) $src += 0x41 - 0x30; // 17
        $src += ((0x2f - $src) >> 8) & 17;

        // if ($src > 0x5a) $src += 0x61 - 0x5b; // 6
        $src += ((0x5a - $src) >> 8) & 6;

        // if ($src > 0x7a) $src += 0x30 - 0x7b; // -75
        $src -= ((0x7a - $src) >> 8) & 75;

        return \pack('C', $src);
    }
}
