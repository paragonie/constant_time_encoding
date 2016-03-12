<?php
use \ParagonIE\ConstantTime\Encoding;

class EncodingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Based on test vectors from RFC 4648
     */
    public function testBase32Encode()
    {
        $this->assertEquals(
            Encoding::base32Encode("\x00"),
            'AA======'
        );
        $this->assertEquals(
            Encoding::base32Encode("\x00\x00"),
            'AAAA===='
        );
        $this->assertEquals(
            Encoding::base32Encode("\x00\x00\x00"),
            'AAAAA==='
        );
        $this->assertEquals(
            Encoding::base32Encode("\x00\x00\x00\x00"),
            'AAAAAAA='
        );
        $this->assertEquals(
            Encoding::base32Encode("\x00\x00\x00\x00\x00"),
            'AAAAAAAA'
        );
        $this->assertEquals(
            Encoding::base32Encode("f"),
            'MY======'
        );
        $this->assertEquals(
            Encoding::base32Encode("fo"),
            'MZXQ===='
        );
        $this->assertEquals(
            Encoding::base32Encode("foo"),
            'MZXW6==='
        );
        $this->assertEquals(
            Encoding::base32Encode("foob"),
            'MZXW6YQ='
        );
        $this->assertEquals(
            Encoding::base32Encode("fooba"),
            'MZXW6YTB'
        );
        $this->assertEquals(
            Encoding::base32Encode("foobar"),
            'MZXW6YTBOI======'
        );
        $this->assertEquals(
            Encoding::base32Encode("\x00\x00\x0F\xFF\xFF"),
            'AAAA7777'
        );
        $this->assertEquals(
            Encoding::base32Encode("\xFF\xFF\xF0\x00\x00"),
            '7777AAAA'
        );

        $this->assertEquals(
            Encoding::base32Encode("\xce\x73\x9c\xe7\x39"),
            'ZZZZZZZZ'
        );
        $this->assertEquals(
            Encoding::base32Encode("\xd6\xb5\xad\x6b\x5a"),
            '22222222'
        );
    }
    /**
     * Based on test vectors from RFC 4648
     */
    public function testBase32Decode()
    {
        $this->assertEquals(
            "\x00\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAAAA======')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAAAAAA====')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAAAAAAA===')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAAAAAAAAA=')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAAAAAAAAAA')
        );
        $this->assertEquals(
            "\x00",
            Encoding::base32Decode('AA======')
        );
        $this->assertEquals(
            "\x00\x00",
            Encoding::base32Decode('AAAA====')
        );
        $this->assertEquals(
            "\x00\x00\x00",
            Encoding::base32Decode('AAAAA===')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAA=')
        );
        $this->assertEquals(
            "\x00\x00\x00\x00\x00",
            Encoding::base32Decode('AAAAAAAA')
        );
        $this->assertEquals(
            "\x00\x00\x0F\xFF\xFF",
            Encoding::base32Decode('AAAA7777')
        );
        $this->assertEquals(
            "\xFF\xFF\xF0\x00\x00",
            Encoding::base32Decode('7777AAAA')
        );
        $this->assertEquals(
            "\xce\x73\x9c\xe7\x39",
            Encoding::base32Decode('ZZZZZZZZ')
        );
        $this->assertEquals(
            "\xd6\xb5\xad\x6b\x5a",
            Encoding::base32Decode('22222222')
        );
        $this->assertEquals(
            'foobar',
            Encoding::base32Decode('MZXW6YTBOI======')
        );

        $rand = random_bytes(9);
        $enc = Encoding::base32Encode($rand);

        $this->assertEquals(
            Encoding::base32Encode($rand),
            Encoding::base32Encode(Encoding::base32Decode($enc))
        );
        $this->assertEquals(
            $rand,
            Encoding::base32Decode($enc)
        );
    }

    /**
     * @covers Encoding::hexDecode()
     * @covers Encoding::hexEncode()
     * @covers Encoding::base32Decode()
     * @covers Encoding::base32Encode()
     * @covers Encoding::base64Decode()
     * @covers Encoding::base64Encode()
     * @covers Encoding::base64DotSlashDecode()
     * @covers Encoding::base64DotSlashEncode()
     * @covers Encoding::base64DotSlashOrderedDecode()
     * @covers Encoding::base64DotSlashOrderedEncode()
     */
    public function testBasicEncoding()
    {
        // Re-run the test at least 3 times for each length
        for ($j = 0; $j < 3; ++$j) {
            for ($i = 1; $i < 84; ++$i) {
                $rand = random_bytes($i);
                $enc = Encoding::hexEncode($rand);
                $this->assertEquals(
                    \bin2hex($rand),
                    $enc,
                    "Hex Encoding - Length: " . $i
                );
                $this->assertEquals(
                    $rand,
                    Encoding::hexDecode($enc),
                    "Hex Encoding - Length: " . $i
                );

                $enc = Encoding::base32Encode($rand);
                $this->assertEquals(
                    $rand,
                    Encoding::base32Decode($enc),
                    "Base32 Encoding - Length: " . $i
                );

                $enc = Encoding::base64Encode($rand);
                $this->assertEquals(
                    $rand,
                    Encoding::base64Decode($enc),
                    "Base64 Encoding - Length: " . $i
                );

                $enc = Encoding::base64EncodeDotSlash($rand);
                $this->assertEquals(
                    $rand,
                    Encoding::base64DecodeDotSlash($enc),
                    "Base64 DotSlash Encoding - Length: " . $i
                );
                $enc = Encoding::base64EncodeDotSlashOrdered($rand);
                $this->assertEquals(
                    $rand,
                    Encoding::base64DecodeDotSlashOrdered($enc),
                    "Base64 Ordered DotSlash Encoding - Length: " . $i
                );
            }
        }
    }
}