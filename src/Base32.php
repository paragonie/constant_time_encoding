<?php
namespace ParagonIE\ConstantTime;

/**
 * Class Base32
 * [A-Z][2-7]
 *
 * @package ParagonIE\ConstantTime
 */
abstract class Base32 implements EncoderInterface
{
    /**
     * Decode a Base32-encoded string into raw binary
     *
     * @param string $src
     * @return string
     */
    public static function decode($src)
    {
        return static::doDecode($src, false);
    }

    /**
     * Decode a Base32-encoded string into raw binary
     *
     * @param string $src
     * @return string
     */
    public static function decodeUpper($src)
    {
        return static::doDecode($src, true);
    }

    /**
     * Encode into Base32 (RFC 4648)
     *
     * @param string $src
     * @return string
     */
    public static function encode($src)
    {
        return static::doEncode($src, false);
    }

    /**
     * Encode into Base32 (RFC 4648)
     *
     * @param string $src
     * @return string
     */
    public static function encodeUpper($src)
    {
        return static::doEncode($src, true);
    }

    /**
     *
     * @param int $src
     * @return int
     */
    protected static function decode5Bits($src)
    {
        $ret = -1;

        // if ($src > 96 && $src < 123) $ret += $src - 97 + 1; // -64
        $ret += (((0x60 - $src) & ($src - 0x7b)) >> 8) & ($src - 96);

        // if ($src > 0x31 && $src < 0x38) $ret += $src - 24 + 1; // -23
        $ret += (((0x31 - $src) & ($src - 0x38)) >> 8) & ($src - 23);

        return $ret;
    }

    /**
     *
     * @param int $src
     * @return int
     */
    protected static function decode5BitsUpper($src)
    {
        $ret = -1;

        // if ($src > 64 && $src < 91) $ret += $src - 65 + 1; // -64
        $ret += (((0x40 - $src) & ($src - 0x5b)) >> 8) & ($src - 64);

        // if ($src > 0x31 && $src < 0x38) $ret += $src - 24 + 1; // -23
        $ret += (((0x31 - $src) & ($src - 0x38)) >> 8) & ($src - 23);

        return $ret;
    }

    /**
     * @param $src
     * @return string
     */
    protected static function encode5Bits($src)
    {
        $diff = 0x61;

        // if ($src > 25) $ret -= 72;
        $diff -= ((25 - $src) >> 8) & 73;

        return \pack('C', $src + $diff);
    }

    /**
     * @param $src
     * @return string
     */
    protected static function encode5BitsUpper($src)
    {
        $diff = 0x41;

        // if ($src > 25) $ret -= 40;
        $diff -= ((25 - $src) >> 8) & 41;

        return \pack('C', $src + $diff);
    }


    /**
     * @param $src
     * @param bool $upper
     * @return string
     */
    protected static function doDecode($src, $upper = false)
    {
        $method = $upper
            ? 'decode5BitsUpper'
            : 'decode5Bits';

        // Remove padding
        $srcLen = Binary::safeStrlen($src);
        if ($srcLen === 0) {
            return '';
        }
        if (($srcLen & 7) === 0) {
            for ($j = 0; $j < 7; ++$j) {
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                } else {
                    break;
                }
            }
        }
        if (($srcLen & 7) === 1) {
            throw new \RangeException(
                'Incorrect padding'
            );
        }

        $err = 0;
        $dest = '';
        for ($i = 0; $i + 8 <= $srcLen; $i += 8) {
            $chunk = \unpack('C*', Binary::safeSubstr($src, $i, 8));
            $c0 = static::$method($chunk[1]);
            $c1 = static::$method($chunk[2]);
            $c2 = static::$method($chunk[3]);
            $c3 = static::$method($chunk[4]);
            $c4 = static::$method($chunk[5]);
            $c5 = static::$method($chunk[6]);
            $c6 = static::$method($chunk[7]);
            $c7 = static::$method($chunk[8]);

            $dest .= \pack(
                'CCCCC',
                (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                (($c3 << 4) | ($c4 >> 1)             ) & 0xff,
                (($c4 << 7) | ($c5 << 2) | ($c6 >> 3)) & 0xff,
                (($c6 << 5) | ($c7     )             ) & 0xff
            );
            $err |= ($c0 | $c1 | $c2 | $c3 | $c4 | $c5 | $c6 | $c7) >> 8;
        }
        if ($i < $srcLen) {
            $chunk = \unpack('C*', Binary::safeSubstr($src, $i, $srcLen - $i));
            $c0 = static::$method($chunk[1]);

            if ($i + 6 < $srcLen) {
                $c1 = static::$method($chunk[2]);
                $c2 = static::$method($chunk[3]);
                $c3 = static::$method($chunk[4]);
                $c4 = static::$method($chunk[5]);
                $c5 = static::$method($chunk[6]);
                $c6 = static::$method($chunk[7]);

                $dest .= \pack(
                    'CCCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff,
                    (($c4 << 7) | ($c5 << 2) | ($c6 >> 3)) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4 | $c5 | $c6) >> 8;
            } elseif ($i + 5 < $srcLen) {
                $c1 = static::$method($chunk[2]);
                $c2 = static::$method($chunk[3]);
                $c3 = static::$method($chunk[4]);
                $c4 = static::$method($chunk[5]);
                $c5 = static::$method($chunk[6]);

                $dest .= \pack(
                    'CCCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff,
                    (($c4 << 7) | ($c5 << 2)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4 | $c5) >> 8;
            } elseif ($i + 4 < $srcLen) {
                $c1 = static::$method($chunk[2]);
                $c2 = static::$method($chunk[3]);
                $c3 = static::$method($chunk[4]);
                $c4 = static::$method($chunk[5]);

                $dest .= \pack(
                    'CCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4) >> 8;
            } elseif ($i + 3 < $srcLen) {
                $c1 = static::$method($chunk[2]);
                $c2 = static::$method($chunk[3]);
                $c3 = static::$method($chunk[4]);

                $dest .= \pack(
                    'CC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
            } elseif ($i + 2 < $srcLen) {
                $c1 = static::$method($chunk[2]);
                $c2 = static::$method($chunk[3]);

                $dest .= \pack(
                    'CC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } elseif ($i + 1 < $srcLen) {
                $c1 = static::$method($chunk[2]);

                $dest .= \pack(
                    'C',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff
                );
                $err |= ($c0 | $c1) >> 8;
            } else {
                $dest .= \pack(
                    'C',
                    (($c0 << 3)                          ) & 0xff
                );
                $err |= ($c0) >> 8;
            }
        }
        if ($err !== 0) {
            throw new \RangeException(
                'base32Decode() only expects characters in the correct base32 alphabet'
            );
        }
        return $dest;
    }

    /**
     * Encode into Base32 (RFC 4648)
     *
     * @param string $src
     * @return string
     */
    protected static function doEncode($src, $upper = false)
    {
        $method = $upper
            ? 'encode5BitsUpper'
            : 'encode5Bits';
        
        $dest = '';
        $srcLen = Binary::safeStrlen($src);
        for ($i = 0; $i + 5 <= $srcLen; $i += 5) {
            $chunk = \unpack('C*', Binary::safeSubstr($src, $i, 5));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $b2 = $chunk[3];
            $b3 = $chunk[4];
            $b4 = $chunk[5];
            $dest .=
                static::$method(              ($b0 >> 3)  & 31) .
                static::$method((($b0 << 2) | ($b1 >> 6)) & 31) .
                static::$method((($b1 >> 1)             ) & 31) .
                static::$method((($b1 << 4) | ($b2 >> 4)) & 31) .
                static::$method((($b2 << 1) | ($b3 >> 7)) & 31) .
                static::$method((($b3 >> 2)             ) & 31) .
                static::$method((($b3 << 3) | ($b4 >> 5)) & 31) .
                static::$method(  $b4                     & 31);
        }
        if ($i < $srcLen) {
            $chunk = \unpack('C*', Binary::safeSubstr($src, $i, $srcLen - $i));
            $b0 = $chunk[1];
            if ($i + 3 < $srcLen) {
                $b1 = $chunk[2];
                $b2 = $chunk[3];
                $b3 = $chunk[4];
                $dest .=
                    static::$method(              ($b0 >> 3)  & 31) .
                    static::$method((($b0 << 2) | ($b1 >> 6)) & 31) .
                    static::$method((($b1 >> 1)             ) & 31) .
                    static::$method((($b1 << 4) | ($b2 >> 4)) & 31) .
                    static::$method((($b2 << 1) | ($b3 >> 7)) & 31) .
                    static::$method((($b3 >> 2)             ) & 31) .
                    static::$method((($b3 << 3)             ) & 31) .
                    '=';
            } elseif ($i + 2 < $srcLen) {
                $b1 = $chunk[2];
                $b2 = $chunk[3];
                $dest .=
                    static::$method(              ($b0 >> 3)  & 31) .
                    static::$method((($b0 << 2) | ($b1 >> 6)) & 31) .
                    static::$method((($b1 >> 1)             ) & 31) .
                    static::$method((($b1 << 4) | ($b2 >> 4)) & 31) .
                    static::$method((($b2 << 1)             ) & 31) .
                    '===';
            } elseif ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
                $dest .=
                    static::$method(              ($b0 >> 3)  & 31) .
                    static::$method((($b0 << 2) | ($b1 >> 6)) & 31) .
                    static::$method((($b1 >> 1)             ) & 31) .
                    static::$method((($b1 << 4)             ) & 31) .
                    '====';
            } else {
                $dest .=
                    static::$method(              ($b0 >> 3)  & 31) .
                    static::$method( ($b0 << 2)               & 31) .
                    '======';
            }
        }
        return $dest;
    }
}