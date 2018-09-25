<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime;

/**
 *  Copyright (c) 2016 - 2018 Paragon Initiative Enterprises.
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

/**
 * Class Hex
 * @package ParagonIE\ConstantTime
 */
abstract class Hex implements EncoderInterface
{
    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @param string $binString (raw binary)
     * @return string
     * @throws \TypeError
     */
    public static function encode(string $binString): string
    {
        /** @var string $hex */
        $hex = '';
        $len = Binary::safeStrlen($binString);
        for ($i = 0; $i < $len; ++$i) {
            /** @var array<int, int> $chunk */
            $chunk = \unpack('C', Binary::safeSubstr($binString, $i, 1));
            /** @var int $c */
            $c = $chunk[1] & 0xf;
            /** @var int $b */
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
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks, returning uppercase letters (as per RFC 4648)
     *
     * @param string $binString (raw binary)
     * @return string
     * @throws \TypeError
     */
    public static function encodeUpper(string $binString): string
    {
        /** @var string $hex */
        $hex = '';
        /** @var int $len */
        $len = Binary::safeStrlen($binString);

        for ($i = 0; $i < $len; ++$i) {
            /** @var array<int, int> $chunk */
            $chunk = \unpack('C', Binary::safeSubstr($binString, $i, 2));
            /** @var int $c */
            $c = $chunk[1] & 0xf;
            /** @var int $b */
            $b = $chunk[1] >> 4;

            $hex .= pack(
                'CC',
                (55 + $b + ((($b - 10) >> 8) & ~6)),
                (55 + $c + ((($c - 10) >> 8) & ~6))
            );
        }
        return $hex;
    }

    /**
     * Convert a hexadecimal string into a binary string without cache-timing
     * leaks
     *
     * @param string $hexString
     * @param bool $strictPadding
     * @return string (raw binary)
     * @throws \RangeException
     */
    public static function decode(string $hexString, bool $strictPadding = false): string
    {
        $hex_len = Binary::safeStrlen($hexString);
        if (($hex_len & 1) !== 0) {
            if ($strictPadding) {
                throw new \RangeException(
                    'Expected an even number of hexadecimal characters'
                );
            } else {
                $hexString = '0' . $hexString;
                ++$hex_len;
            }
        }

        $block_size = PHP_INT_SIZE << 1;

        $pad = $block_size - ($hex_len % $block_size);
        $offset = $pad != $block_size ? $pad : 0;

        $hexString = str_repeat('0', $offset) . $hexString;

        $str = preg_replace_callback(
            '#[0-9a-fA-F]{' . $block_size . '}#',
            function ($matches) {
                $msb = ord($matches[0][0]);
                $xor = $msb >= 56 ? PHP_INT_MIN : 0;
                $msb|= 0x20;
                $matches[0][0] = $msb <= 57 ?
                    chr($msb & 0xF7) : 
                    chr($msb - 47);
                return pack(
                    PHP_INT_SIZE == 4 ? 'N' : 'J',
                    $xor ^ eval('return 0x' . $matches[0] . ';')
                );
            },
            $hexString
        );

        return substr($str, $offset >> 1);
    }
}
