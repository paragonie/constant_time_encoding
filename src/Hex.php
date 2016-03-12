<?php
namespace ParagonIE\ConstantTime;

abstract class Hex
{
    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @param string $bin_string (raw binary)
     * @return string
     */
    public static function encode($bin_string)
    {
        $hex = '';
        $len = Core::safeStrlen($bin_string);
        for ($i = 0; $i < $len; ++$i) {
            $chunk = \unpack('C', Core::safeSubstr($bin_string, $i, 2));
            $c = $chunk[1] & 0xf;
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
     * @throws \RangeException
     */
    public static function decode($hex_string)
    {
        $hex_pos = 0;
        $bin = '';
        $c_acc = 0;
        $hex_len = Core::safeStrlen($hex_string);
        $state = 0;

        $chunk = \unpack('C*', $hex_string);
        while ($hex_pos < $hex_len) {
            ++$hex_pos;
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
            $state ^= 1;
        }
        return $bin;
    }


}