<?php
namespace ParagonIE\ConstantTime;

interface EncoderInterface
{
    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @param string $bin_string (raw binary)
     * @return string
     */
    public static function encode($bin_string);

    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @param string $encoded_string
     * @return string (raw binary)
     */
    public static function decode($encoded_string);

}