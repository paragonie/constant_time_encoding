<?php
namespace ParagonIE\ConstantTime;

abstract class Binary
{
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