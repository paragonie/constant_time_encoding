<?php
use \ParagonIE\ConstantTime\Encoding;

class EncodingTest extends PHPUnit_Framework_TestCase
{
    public function testBasicEncoding()
    {
        $str = random_bytes(33);
        $enc = base64_encode($str);
        $this->assertEquals(
            $str,
            Encoding::base64Decode($enc)
        );
    }
}