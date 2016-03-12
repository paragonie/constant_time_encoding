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
     * Decode a Base32-encoded string into raw binary
     *
     * @param string $src
     * @return string
     */
    public static function base32Decode($src)
    {
        // Remove padding
        $srcLen = self::safeStrlen($src);
        if ($srcLen === 0) {
            return '';
        }
        if (($srcLen & 7) === 0) {
            if ($src[$srcLen - 1] === '=') {
                $srcLen--;
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                }
            }
        }
        if (($srcLen & 7) === 1) {
            return false;
        }

        $err = 0;
        $dest = '';
        for ($i = 0; $i + 8 <= $srcLen; $i += 8) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, 8));
            $c0 = self::base32Decode5Bits($chunk[1]);
            $c1 = self::base32Decode5Bits($chunk[2]);
            $c2 = self::base32Decode5Bits($chunk[3]);
            $c3 = self::base32Decode5Bits($chunk[4]);
            $c4 = self::base32Decode5Bits($chunk[5]);
            $c5 = self::base32Decode5Bits($chunk[6]);
            $c6 = self::base32Decode5Bits($chunk[7]);
            $c7 = self::base32Decode5Bits($chunk[8]);

            $dest .= pack(
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $c0 = self::base32Decode5Bits($chunk[1]);
            if ($i + 6 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);
                $c2 = self::base32Decode5Bits($chunk[3]);
                $c3 = self::base32Decode5Bits($chunk[4]);
                $c4 = self::base32Decode5Bits($chunk[5]);
                $c5 = self::base32Decode5Bits($chunk[6]);
                $c6 = self::base32Decode5Bits($chunk[7]);

                $dest .= pack(
                    'CCCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff,
                    (($c4 << 7) | ($c5 << 2) | ($c6 >> 3)) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4 | $c5 | $c6) >> 8;
            } elseif ($i + 5 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);
                $c2 = self::base32Decode5Bits($chunk[3]);
                $c3 = self::base32Decode5Bits($chunk[4]);
                $c4 = self::base32Decode5Bits($chunk[5]);
                $c5 = self::base32Decode5Bits($chunk[6]);

                $dest .= pack(
                    'CCCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff,
                    (($c4 << 7) | ($c5 << 2)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4 | $c5) >> 8;
            } elseif ($i + 4 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);
                $c2 = self::base32Decode5Bits($chunk[3]);
                $c3 = self::base32Decode5Bits($chunk[4]);
                $c4 = self::base32Decode5Bits($chunk[5]);

                $dest .= pack(
                    'CCC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff,
                    (($c3 << 4) | ($c4 >> 1)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3 | $c4) >> 8;
            } elseif ($i + 3 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);
                $c2 = self::base32Decode5Bits($chunk[3]);
                $c3 = self::base32Decode5Bits($chunk[4]);

                $dest .= pack(
                    'CC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1) | ($c3 >> 4)) & 0xff
                );
                $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
            } elseif ($i + 2 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);
                $c2 = self::base32Decode5Bits($chunk[3]);

                $dest .= pack(
                    'CC',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff,
                    (($c1 << 6) | ($c2 << 1)             ) & 0xff
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } elseif ($i + 1 < $srcLen) {
                $c1 = self::base32Decode5Bits($chunk[2]);

                $dest .= pack(
                    'C',
                    (($c0 << 3) | ($c1 >> 2)             ) & 0xff
                );
                $err |= ($c0 | $c1) >> 8;
            } else {
                $dest .= pack(
                    'C',
                    (($c0 << 3)                          ) & 0xff
                );
                $err |= ($c0) >> 8;
            }
        }
        if ($err !== 0) {
            return false;
        }
        return $dest;
    }

    /**
     * Encode into Base32 (RFC 4648)
     *
     * @param string $src
     * @return string
     */
    public static function base32Encode($src)
    {
        $dest = '';
        $srcLen = self::safeStrlen($src);
        for ($i = 0; $i + 5 <= $srcLen; $i += 5) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, 5));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $b2 = $chunk[3];
            $b3 = $chunk[4];
            $b4 = $chunk[5];
            $dest .=
                self::base32Encode5Bits(              ($b0 >> 3)  & 31) .
                self::base32Encode5Bits((($b0 << 2) | ($b1 >> 6)) & 31) .
                self::base32Encode5Bits((($b1 >> 1)             ) & 31) .
                self::base32Encode5Bits((($b1 << 4) | ($b2 >> 4)) & 31) .
                self::base32Encode5Bits((($b2 << 1) | ($b3 >> 7)) & 31) .
                self::base32Encode5Bits((($b3 >> 2)             ) & 31) .
                self::base32Encode5Bits((($b3 << 3) | ($b4 >> 5)) & 31) .
                self::base32Encode5Bits(  $b4                     & 31);
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $b0 = $chunk[1];
            if ($i + 3 < $srcLen) {
                $b1 = $chunk[2];
                $b2 = $chunk[3];
                $b3 = $chunk[4];
                $dest .=
                    self::base32Encode5Bits(              ($b0 >> 3)  & 31) .
                    self::base32Encode5Bits((($b0 << 2) | ($b1 >> 6)) & 31) .
                    self::base32Encode5Bits((($b1 >> 1)             ) & 31) .
                    self::base32Encode5Bits((($b1 << 4) | ($b2 >> 4)) & 31) .
                    self::base32Encode5Bits((($b2 << 1) | ($b3 >> 7)) & 31) .
                    self::base32Encode5Bits((($b3 >> 2)             ) & 31) .
                    self::base32Encode5Bits((($b3 << 3)             ) & 31) .
                    '=';
            } elseif ($i + 2 < $srcLen) {
                $b1 = $chunk[2];
                $b2 = $chunk[3];
                $dest .=
                    self::base32Encode5Bits(              ($b0 >> 3)  & 31) .
                    self::base32Encode5Bits((($b0 << 2) | ($b1 >> 6)) & 31) .
                    self::base32Encode5Bits((($b1 >> 1)             ) & 31) .
                    self::base32Encode5Bits((($b1 << 4) | ($b2 >> 4)) & 31) .
                    self::base32Encode5Bits((($b2 << 1)             ) & 31) .
                    '===';
            } elseif ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
                $dest .=
                    self::base32Encode5Bits(              ($b0 >> 3)  & 31) .
                    self::base32Encode5Bits((($b0 << 2) | ($b1 >> 6)) & 31) .
                    self::base32Encode5Bits((($b1 >> 1)             ) & 31) .
                    self::base32Encode5Bits((($b1 << 4)             ) & 31) .
                    '====';
            } else {
                $dest .=
                    self::base32Encode5Bits(              ($b0 >> 3)  & 31) .
                    self::base32Encode5Bits( ($b0 << 2)               & 31) .
                    '======';
            }
        }
        return $dest;
    }

    /**
     * Encode into Base64
     *
     * Base64 character set "[A-Z][a-z][0-9]+/"
     *
     * @param string $src
     * @return string
     */
    public static function base64Encode($src)
    {
        $dest = '';
        $srcLen = self::safeStrlen($src);
        for ($i = 0; $i + 3 <= $srcLen; $i += 3) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, 3));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $b2 = $chunk[3];

            $dest .=
                self::base64Encode6Bits(               $b0 >> 2       ) .
                self::base64Encode6Bits((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6Bits((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6Bits(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $b0 = $chunk[1];
            if ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, 4));
            $c0 = self::base64Decode6Bits($chunk[1]);
            $c1 = self::base64Decode6Bits($chunk[2]);
            $c2 = self::base64Decode6Bits($chunk[3]);
            $c3 = self::base64Decode6Bits($chunk[4]);

            $dest .= pack(
                'CCC',
                ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                ((($c2 << 6) |  $c3      ) & 0xff)
            );
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $c0 = self::base64Decode6Bits($chunk[1]);
            $c1 = self::base64Decode6Bits($chunk[2]);
            if ($i + 2 < $srcLen) {
                $c1 = self::base64Decode6Bits($chunk[3]);

                $dest .= pack(
                    'CCC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                    ((($c2 << 6)             ) & 0xff)
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .= pack(
                    'CCC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4)             ) & 0xff)
                );
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, 3));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $b2 = $chunk[3];

            $dest .=
                self::base64Encode6BitsDotSlash(               $b0 >> 2       ) .
                self::base64Encode6BitsDotSlash((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6BitsDotSlash((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6BitsDotSlash(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, 3));
            $b0 = $chunk[1];
            if ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, 4));
            $c0 = self::base64Decode6BitsDotSlash($chunk[1]);
            $c1 = self::base64Decode6BitsDotSlash($chunk[2]);
            $c2 = self::base64Decode6BitsDotSlash($chunk[3]);
            $c3 = self::base64Decode6BitsDotSlash($chunk[4]);

            $dest .= pack(
                'CCC',
                ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                ((($c2 << 6) |  $c3      ) & 0xff)
            );
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $c0 = self::base64Decode6BitsDotSlash($chunk[1]);
            $c1 = self::base64Decode6BitsDotSlash($chunk[2]);
            if ($i + 2 < $srcLen) {
                $c2 = self::base64Decode6BitsDotSlash($chunk[3]);
                $dest .= pack(
                    'CCC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                    ((($c2 << 6)             ) & 0xff)
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .= pack(
                    'CC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4)             ) & 0xff)
                );
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, 3));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $dest .=
                self::base64Encode6BitsDotSlashOrdered(               $b0 >> 2       ) .
                self::base64Encode6BitsDotSlashOrdered((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::base64Encode6BitsDotSlashOrdered((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::base64Encode6BitsDotSlashOrdered(  $b2                     & 63);
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, 3));
            $b0 = $chunk[1];
            $b0 = ord($src[$i]);
            if ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
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
            $chunk = unpack('C*', self::safeSubstr($src, $i, 4));
            $c0 = self::base64Decode6BitsDotSlashOrdered($chunk[1]);
            $c1 = self::base64Decode6BitsDotSlashOrdered($chunk[2]);
            $c2 = self::base64Decode6BitsDotSlashOrdered($chunk[3]);
            $c3 = self::base64Decode6BitsDotSlashOrdered($chunk[4]);

            $dest .= pack(
                'CCC',
                ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                ((($c2 << 6) |  $c3      ) & 0xff)
            );
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        if ($i < $srcLen) {
            $chunk = unpack('C*', self::safeSubstr($src, $i, $srcLen - $i));
            $c0 = self::base64Decode6BitsDotSlashOrdered($chunk[1]);
            $c1 = self::base64Decode6BitsDotSlashOrdered($chunk[2]);
            if ($i + 2 < $srcLen) {
                $c2 = self::base64Decode6BitsDotSlashOrdered($chunk[3]);
                $dest .= pack(
                    'CCC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                    ((($c2 << 6)             ) & 0xff)
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } else {
                $dest .= pack(
                    'CC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4)             ) & 0xff)
                );
                $err |= ($c0 | $c1) >> 8;
            }
        }
        if ($err !== 0) {
            return false;
        }
        return $dest;
    }

    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @param string $bin_string (raw binary)
     * @return string
     */
    public static function hexEncode($bin_string)
    {
        $hex = '';
        $len = self::safeStrlen($bin_string);
        for ($i = 0; $i < $len; ++$i) {
            $chunk = unpack('C*', self::safeSubstr($bin_string, $i, 2));
            $c = $chunk[0] & 0xf;
            $b = $chunk[1] >> 4;
            $hex .= pack(
                'CC',
                (87 + $b + ((($b - 10) >> 8) & ~38)),
                (87 + $c + ((($c - 10) >> 8) & ~38))
            );
        }
        return $hex;
    }

    /**
     * Convert a hexadecimal string into a binary string without cache-timing
     * leaks
     *
     * @param string $hex_string
     * @return string (raw binary)
     */
    public static function hexDecode($hex_string)
    {
        $hex_pos = 0;
        $bin = '';
        $c_acc = 0;
        $hex_len = self::safeStrlen($hex_string);
        $state = 0;

        $chunk = unpack('C*', $hex_string);
        while ($hex_pos < $hex_len) {
            $c = $chunk[$hex_pos];
            $c_num = $c ^ 48;
            $c_num0 = ($c_num - 10) >> 8;
            $c_alpha = ($c & ~32) - 55;
            $c_alpha0 = (($c_alpha - 10) ^ ($c_alpha - 16)) >> 8;
            if (($c_num0 | $c_alpha0) === 0) {
                throw new \RangeException(
                    'hexEncode() only expects hexadecimal characters'
                );
            }
            $c_val = ($c_num0 & $c_num) | ($c_alpha & $c_alpha0);
            if ($state === 0) {
                $c_acc = $c_val * 16;
            } else {
                $bin .= \pack('C', $c_acc | $c_val);
            }
            $state = $state ? 0 : 1;
            ++$hex_pos;
        }
        return $bin;
    }

    /**
     *
     * @param $src
     * @return int
     */
    protected static function base32Decode5Bits($src)
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
    protected static function base32Encode5Bits($src)
    {
        $diff = 0x41;

        // if ($src > 25) $ret -= 40;
        $diff -= ((25 - $src) >> 8) & 41;

        return \pack('C', $src + $diff);
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

        return \pack('C', $src + $diff);
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

        return \pack('C', $src);
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

        return \pack('C', $src);
    }

    /**
     * Safe string length
     *
     * @ref mbstring.func_overload
     *
     * @param string $str
     * @return int
     */
    protected static function safeStrlen($str)
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, '8bit');
        } else {
            return \strlen($str);
        }
    }

    /**
     * Safe substring
     *
     * @ref mbstring.func_overload
     *
     * @staticvar boolean $exists
     * @param string $str
     * @param int $start
     * @param int $length
     * @return string
     * @throws \TypeError
     */
    public static function safeSubstr(
        $str,
        $start = 0,
        $length = null
    ) {
        if (\function_exists('mb_substr')) {
            // mb_substr($str, 0, NULL, '8bit') returns an empty string on PHP
            // 5.3, so we have to find the length ourselves.
            if ($length === null) {
                if ($start >= 0) {
                    $length = self::safeStrlen($str) - $start;
                } else {
                    $length = -$start;
                }
            }
            // $length calculation above might result in a 0-length string
            if ($length === 0) {
                return '';
            }
            return \mb_substr($str, $start, $length, '8bit');
        }
        if ($length === 0) {
            return '';
        }
        // Unlike mb_substr(), substr() doesn't accept NULL for length
        if ($length !== null) {
            return \substr($str, $start, $length);
        } else {
            return \substr($str, $start);
        }
    }
}
