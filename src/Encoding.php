<?php
namespace ParagonIE\ConstantTime;

/**
 *  Copyright (c) 2014 Steve "Sc00bz" Thomas (steve at tobtu dot com)
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 */
class Encoding
{
    /**
     * Encode into Base64
     *
     * Base64 character set "[A-Z][a-z][0-9]+/"
     *
     * @param $src
     * @return string
     */
    public static function base64Encode($src)
    {
        $dest = '';
        $srcLen = self::safeStrlen($src);
        for ($i = 0; $i + 3 <= $srcLen; $i += 3) {
            $b0 = ord($src[$i]);
            $b1 = ord($src[$i + 1]);
            $b2 = ord($src[$i + 2]);

            $dest .=
                self::base64Encode6Bits(               $b0 >> 2       ) .
                self::base64Encode6Bits((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6Bits((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6Bits(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $b0 = ord($src[$i]);
            if ($i + 1 < $srcLen) {
                $b1 = ord($src[$i + 1]);
                $dest .=
                    self::base64Encode6Bits(               $b0 >> 2       ) .
                    self::base64Encode6Bits((($b0 << 4) | ($b1 >> 4)) & 63) .
                    self::base64Encode6Bits( ($b1 << 2)               & 63) . '=';
            } else {
                $dest .=
                    self::base64Encode6Bits( $b0 >> 2) .
                    self::base64Encode6Bits(($b0 << 4) & 63) . '==';
            }
        }
        return $dest;
    }

    /**
     * Decode from base64 into binary
     *
     * Base64 character set "./[A-Z][a-z][0-9]"
     *
     * @param $src
     * @return bool|string
     */
    public static function base64Decode($src)
    {
        // Remove padding
        $srcLen = self::safeStrlen($src);
        if ($srcLen === 0) {
            return '';
        }
        if (($srcLen & 3) === 0) {
            if ($src[$srcLen - 1] === '=') {
                $srcLen--;
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
            }
        }
        if (($srcLen & 3) === 1) {
            return false;
        }

        $err = 0;
        $dest = '';
        for ($i = 0; $i + 4 <= $srcLen; $i += 4) {
            $c0 = self::base64Decode6Bits(ord($src[$i]));
            $c1 = self::base64Decode6Bits(ord($src[$i + 1]));
            $c2 = self::base64Decode6Bits(ord($src[$i + 2]));
            $c3 = self::base64Decode6Bits(ord($src[$i + 3]));

            $dest .=
                chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                chr((($c2 << 6) |  $c3      ) & 0xff);
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $c0 = self::base64Decode6Bits(ord($src[$i]));
            $c1 = self::base64Decode6Bits(ord($src[$i + 1]));
            if ($i + 2 < $srcLen) {
                $c2 = self::base64Decode6Bits(ord($src[$i + 2]));
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                    chr( ($c2 << 6)               & 0xff);
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr( ($c1 << 4)               & 0xff);
                $err |= ($c0 | $c1) >> 8;
            }
        }
        if ($err !== 0) {
            return false;
        }
        return $dest;
    }

    /**
     * Encode into Base64
     *
     * Base64 character set "./[A-Z][a-z][0-9]"
     * @param $src
     * @return string
     */
    public static function base64EncodeDotSlash($src)
    {
        $dest = '';
        $srcLen = self::safeStrlen($src);
        for ($i = 0; $i + 3 <= $srcLen; $i += 3) {
            $b0 = ord($src[$i]);
            $b1 = ord($src[$i + 1]);
            $b2 = ord($src[$i + 2]);

            $dest .=
                self::base64Encode6BitsDotSlash(               $b0 >> 2       ) .
                self::base64Encode6BitsDotSlash((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6BitsDotSlash((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6BitsDotSlash(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $b0 = ord($src[$i]);
            if ($i + 1 < $srcLen) {
                $b1 = ord($src[$i + 1]);
                $dest .=
                    self::base64Encode6BitsDotSlash(               $b0 >> 2       ) .
                    self::base64Encode6BitsDotSlash((($b0 << 4) | ($b1 >> 4)) & 63) .
                    self::base64Encode6BitsDotSlash( ($b1 << 2)               & 63) . '=';
            } else {
                $dest .=
                    self::base64Encode6BitsDotSlash( $b0 >> 2) .
                    self::base64Encode6BitsDotSlash(($b0 << 4) & 63) . '==';
            }
        }
        return $dest;
    }

    /**
     * Decode from base64 to raw binary
     *
     * Base64 character set "./[A-Z][a-z][0-9]"
     *
     * @param $src
     * @return bool|string
     */
    public static function base64DecodeDotSlash($src)
    {
        // Remove padding
        $srcLen = self::safeStrlen($src);
        if ($srcLen === 0) {
            return '';
        }
        if (($srcLen & 3) === 0) {
            if ($src[$srcLen - 1] === '=') {
                $srcLen--;
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
            }
        }
        if (($srcLen & 3) == 1) {
            return false;
        }

        $err = 0;
        $dest = '';
        for ($i = 0; $i + 4 <= $srcLen; $i += 4) {
            $c0 = self::base64Decode6BitsDotSlash(ord($src[$i]));
            $c1 = self::base64Decode6BitsDotSlash(ord($src[$i + 1]));
            $c2 = self::base64Decode6BitsDotSlash(ord($src[$i + 2]));
            $c3 = self::base64Decode6BitsDotSlash(ord($src[$i + 3]));

            $dest .=
                chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                chr((($c2 << 6) |  $c3      ) & 0xff);
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $c0 = self::base64Decode6BitsDotSlash(ord($src[$i]));
            $c1 = self::base64Decode6BitsDotSlash(ord($src[$i + 1]));
            if ($i + 2 < $srcLen) {
                $c2 = self::base64Decode6BitsDotSlash(ord($src[$i + 2]));
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                    chr( ($c2 << 6)               & 0xff);
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr( ($c1 << 4)               & 0xff);
                $err |= ($c0 | $c1) >> 8;
            }
        }
        if ($err !== 0) {
            return false;
        }
        return $dest;
    }

    /**
     * Encode into Base64
     *
     * Base64 character set "[.-9][A-Z][a-z]" or "./[0-9][A-Z][a-z]"
     * @param $src
     * @return string
     */
    public static function base64EncodeDotSlashOrdered($src)
    {
        $dest = '';
        $srcLen = self::safeStrlen($src);
        for ($i = 0; $i + 3 <= $srcLen; $i += 3) {
            $b0 = ord($src[$i]);
            $b1 = ord($src[$i + 1]);
            $b2 = ord($src[$i + 2]);

            $dest .=
                self::base64Encode6BitsDotSlashOrdered(               $b0 >> 2       ) .
                self::base64Encode6BitsDotSlashOrdered((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6BitsDotSlashOrdered((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6BitsDotSlashOrdered(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $b0 = ord($src[$i]);
            if ($i + 1 < $srcLen) {
                $b1 = ord($src[$i + 1]);
                $dest .=
                    self::base64Encode6BitsDotSlashOrdered(               $b0 >> 2       ) .
                    self::base64Encode6BitsDotSlashOrdered((($b0 << 4) | ($b1 >> 4)) & 63) .
                    self::base64Encode6BitsDotSlashOrdered( ($b1 << 2)               & 63) . '=';
            } else {
                $dest .=
                    self::base64Encode6BitsDotSlashOrdered( $b0 >> 2) .
                    self::base64Encode6BitsDotSlashOrdered(($b0 << 4) & 63) . '==';
            }
        }
        return $dest;
    }


    /**
     * Decode from base64 to raw binary
     *
     * Base64 character set "[.-9][A-Z][a-z]" or "./[0-9][A-Z][a-z]"
     *
     * @param $src
     * @return bool|string
     */
    public static function base64DecodeDotSlashOrdered($src)
    {
        // Remove padding
        $srcLen = self::safeStrlen($src);
        if ($srcLen === 0) {
            return '';
        }
        if (($srcLen & 3) === 0) {
            if ($src[$srcLen - 1] === '=') {
                $srcLen--;
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
            }
        }
        if (($srcLen & 3) == 1) {
            return false;
        }

        $err = 0;
        $dest = '';
        for ($i = 0; $i + 4 <= $srcLen; $i += 4) {
            $c0 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i]));
            $c1 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i + 1]));
            $c2 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i + 2]));
            $c3 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i + 3]));

            $dest .=
                chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                chr((($c2 << 6) |  $c3      ) & 0xff);
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $c0 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i]));
            $c1 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i + 1]));
            if ($i + 2 < $srcLen) {
                $c2 = self::base64Decode6BitsDotSlashOrdered(ord($src[$i + 2]));
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr((($c1 << 4) | ($c2 >> 2)) & 0xff) .
                    chr( ($c2 << 6)               & 0xff);
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .=
                    chr((($c0 << 2) | ($c1 >> 4)) & 0xff) .
                    chr( ($c1 << 4)               & 0xff);
                $err |= ($c0 | $c1) >> 8;
            }
        }
        if ($err !== 0) {
            return false;
        }
        return $dest;
    }

    /**
     *
     * Base64 character set:
     * [A-Z]      [a-z]      [0-9]      +     /
     * 0x41-0x5a, 0x61-0x7a, 0x30-0x39, 0x2b, 0x2f
     *
     * @param $src
     * @return int
     */
    protected static function base64Decode6Bits($src)
    {
        $ret = -1;

        // if ($src > 0x40 && $src < 0x5b) $ret += $src - 0x41 + 1; // -64
        $ret += (((0x40 - $src) & ($src - 0x5b)) >> 8) & ($src - 64);

        // if ($src > 0x60 && $src < 0x7b) $ret += $src - 0x61 + 26 + 1; // -70
        $ret += (((0x60 - $src) & ($src - 0x7b)) >> 8) & ($src - 70);

        // if ($src > 0x2f && $src < 0x3a) $ret += $src - 0x30 + 52 + 1; // 5
        $ret += (((0x2f - $src) & ($src - 0x3a)) >> 8) & ($src + 5);

        // if ($src == 0x2b) $ret += 62 + 1;
        $ret += (((0x2a - $src) & ($src - 0x2c)) >> 8) & 63;

        // if ($src == 0x2f) ret += 63 + 1;
        $ret += (((0x2e - $src) & ($src - 0x30)) >> 8) & 64;

        return $ret;
    }

    /**
     * @param $src
     * @return string
     */
    protected static function base64Encode6Bits($src)
    {
        $diff = 0x41;

        // if ($src > 25) $diff += 0x61 - 0x41 - 26; // 6
        $diff += ((25 - $src) >> 8) & 6;

        // if ($src > 51) $diff += 0x30 - 0x61 - 26; // -75
        $diff -= ((51 - $src) >> 8) & 75;

        // if ($src > 61) $diff += 0x2b - 0x30 - 10; // -15
        $diff -= ((61 - $src) >> 8) & 15;

        // if ($src > 62) $diff += 0x2f - 0x2b - 1; // 3
        $diff += ((62 - $src) >> 8) & 3;

        return chr($src + $diff);
    }

    /**
     * Base64 character set:
     * ./         [A-Z]      [a-z]     [0-9]
     * 0x2e-0x2f, 0x41-0x5a, 0x61-0x7a, 0x30-0x39
     *
     * @param $src
     * @return int
     */
    protected static function base64Decode6BitsDotSlash($src)
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
     * @param $src
     * @return string
     */
    protected static function base64Encode6BitsDotSlash($src)
    {
        $src += 0x2e;

        // if ($src > 0x2f) $src += 0x41 - 0x30; // 17
        $src += ((0x2f - $src) >> 8) & 17;

        // if ($src > 0x5a) $src += 0x61 - 0x5b; // 6
        $src += ((0x5a - $src) >> 8) & 6;

        // if ($src > 0x7a) $src += 0x30 - 0x7b; // -75
        $src -= ((0x7a - $src) >> 8) & 75;

        return chr($src);
    }

    /**
     * Base64 character set:
     * [.-9]      [A-Z]      [a-z]
     * 0x2e-0x39, 0x41-0x5a, 0x61-0x7a
     * @param $src
     * @return int
     */
    protected static function base64Decode6BitsDotSlashOrdered($src)
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
     * Base64 character set:
     * [.-9]      [A-Z]      [a-z]
     * 0x2e-0x39, 0x41-0x5a, 0x61-0x7a
     * @param $src
     * @return string
     */
    protected static function base64Encode6BitsDotSlashOrdered($src)
    {
        $src += 0x2e;

        // if ($src > 0x39) $src += 0x41 - 0x3a; // 7
        $src += ((0x39 - $src) >> 8) & 7;

        // if ($src > 0x5a) $src += 0x61 - 0x5b; // 6
        $src += ((0x5a - $src) >> 8) & 6;

        return chr($src);
    }

    /**
     * Safe string length
     *
     * @ref mbstring.func_overload
     *
     * @param string $str
     * @return int
     */
    public static function safeStrlen($str)
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, '8bit');
        } else {
            return \strlen($str);
        }
    }
}
