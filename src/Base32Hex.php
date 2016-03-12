<?php
namespace ParagonIE\ConstantTime;

/**
 * Class Base32Hex
 * [0-9][A-V]
 *
 * @package ParagonIE\ConstantTime
 */
abstract class Base32Hex extends Base32
{
    /**
     * Base64 character set:
     * [0-9]      [A-V]
     * 0x30-0x39, 0x41-0x56
     *
     * @param int $src
     * @return int
     */
    protected static function decode5Bits($src)
    {
        $ret = -1;

        // if ($src > 0x30 && $src < 0x3a) ret += $src - 0x2e + 1; // -47
        $ret += (((0x2f - $src) & ($src - 0x3a)) >> 8) & ($src - 47);

        // if ($src > 0x40 && $src < 0x57) ret += $src - 0x41 + 10 + 1; // -54
        $ret += (((0x40 - $src) & ($src - 0x57)) >> 8) & ($src - 54);

        return $ret;
    }

    /**
     * @param int $src
     * @return string
     */
    protected static function encode5Bits($src)
    {
        $src += 0x30;

        // if ($src > 0x39) $src += 0x41 - 0x3a; // 7
        $src += ((0x39 - $src) >> 8) & 7;

        return \pack('C', $src);
    }
}