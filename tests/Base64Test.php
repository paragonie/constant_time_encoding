<?php
use \ParagonIE\ConstantTime\Base64;

class Base64Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Base64::encode()
     * @covers Base64::decode()
     */
    public function testRandom()
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = \random_bytes($i);

                $enc = Base64::encode($random);
                $this->assertSame(
                    $random,
                    Base64::decode($enc)
                );
                $this->assertSame(
                    \base64_encode($random),
                    $enc
                );
            }
        }
    }
}
