<?php
namespace ParagonIE\ConstantTime;

/**
 * Class RFC4648
 *
 * This class conforms strictly to the RFC
 *
 * @package ParagonIE\ConstantTime
 */
abstract class RFC4648
{
    /**
     * RFC 4648 Base64 encoding
     *
     * "foo" -> "Zm9v"
     *
     * @param string $str
     * @return string
     */
    public function base64Encode($str)
    {
        return Base64::encode($str);
    }

    /**
     * RFC 4648 Base64 decoding
     *
     * "Zm9v" -> "foo"
     *
     * @param string $str
     * @return string
     */
    public function base64Decode($str)
    {
        return Base64::decode($str);
    }

    /**
     * RFC 4648 Base64 (URL Safe) encoding
     *
     * "foo" -> "Zm9v"
     *
     * @param string $str
     * @return string
     */
    public function base64UrlSafeEncode($str)
    {
        return Base64UrlSafe::encode($str);
    }

    /**
     * RFC 4648 Base64 (URL Safe) decoding
     *
     * "Zm9v" -> "foo"
     *
     * @param string $str
     * @return string
     */
    public function base64UrlSafeDecode($str)
    {
        return Base64UrlSafe::decode($str);
    }

    /**
     * RFC 4648 Base32 encoding
     *
     * "foo" -> "MZXW6==="
     *
     * @param string $str
     * @return string
     */
    public function base32Encode($str)
    {
        return Base32::encodeUpper($str);
    }

    /**
     * RFC 4648 Base32 encoding
     *
     * "MZXW6===" -> "foo"
     *
     * @param string $str
     * @return string
     */
    public function base32Decode($str)
    {
        return Base32::decodeUpper($str);
    }

    /**
     * RFC 4648 Base32-Hex encoding
     *
     * "foo" -> "CPNMU==="
     *
     * @param string $str
     * @return string
     */
    public function base32HexEncode($str)
    {
        return Base32::encodeUpper($str);
    }

    /**
     * RFC 4648 Base32-Hex decoding
     *
     * "CPNMU===" -> "foo"
     *
     * @param string $str
     * @return string
     */
    public function base32HexDecode($str)
    {
        return Base32::decodeUpper($str);
    }

    /**
     * RFC 4648 Base16 decoding
     *
     * "foo" -> "666F6F"
     *
     * @param string $str
     * @return string
     */
    public function base16Encode($str)
    {
        return Hex::encodeUpper($str);
    }

    /**
     * RFC 4648 Base16 decoding
     *
     * "666F6F" -> "foo"
     *
     * @param string $str
     * @return string
     */
    public function base16Decode($str)
    {
        return Hex::decode($str);
    }
}