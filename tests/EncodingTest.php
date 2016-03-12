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

    public function testBasicEncoding()
    {
        $str = random_bytes(33);
        $enc = base64_encode($str);
        $this->assertEquals(
            $str,
            Encoding::base64Decode($enc)
        );

        for ($i = 1; $i < 34; ++$i) {
            $rand = random_bytes($i);
            $enc = Encoding::base32Encode($rand);
            $this->assertEquals(
                bin2hex($rand),
                bin2hex(Encoding::base32Decode($enc))
            );
        }
    }
}