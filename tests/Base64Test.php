<?php
declare(strict_types=1);
namespace ParagonIE\ConstantTime\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ParagonIE\ConstantTime\Base64;
use RangeException;
use function base64_decode;
use function base64_encode;
use function bin2hex;
use function random_bytes;
use function rtrim;

class Base64Test extends TestCase
{
    use CanonicalTrait;

    /**
     * @throws Exception
     */
    public function testRandom(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            for ($j = 0; $j < 50; ++$j) {
                $random = random_bytes($i);

                $enc = Base64::encode($random);
                $this->assertSame(
                    $random,
                    Base64::decode($enc)
                );
                $this->assertSame(
                    base64_encode($random),
                    $enc
                );

                $unpadded = rtrim($enc, '=');
                $this->assertSame(
                    $random,
                    Base64::decode($unpadded)
                );
                $this->assertSame(
                    $random,
                    Base64::decode($unpadded, true)
                );
            }
        }
        $str = 'MIIFzzCCBLegAwIBAgIDAfdlMA0GCSqGSIb3DQEBBQUAMHMxCzAJBgNVBAYTAlBM' .
            'MSgwJgYDVQQKDB9LcmFqb3dhIEl6YmEgUm96bGljemVuaW93YSBTLkEuMSQwIgYDVQQ' .
            'DDBtDT1BFIFNaQUZJUiAtIEt3YWxpZmlrb3dhbnkxFDASBgNVBAUTC05yIHdwaXN1Oi' .
            'A2MB4XDTExMTEwOTA2MDAwMFoXDTEzMTEwOTA2MDAwMFowgdkxCzAJBgNVBAYTAlBMM' .
            'RwwGgYDVQQKDBNVcnrEhWQgTWlhc3RhIEdkeW5pMRswGQYDVQQFExJQRVNFTDogNjEw' .
            'NjA2MDMxMTgxGTAXBgNVBAMMEEplcnp5IFByemV3b3Jza2kxTzBNBgNVBBAwRgwiQWw' .
            'uIE1hcnN6YcWCa2EgUGnFgnN1ZHNraWVnbyA1Mi81NAwNODEtMzgyIEdkeW5pYQwGUG' .
            '9sc2thDAlwb21vcnNraWUxDjAMBgNVBCoMBUplcnp5MRMwEQYDVQQEDApQcnpld29yc' .
            '2tpMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCMm5vjGqHPthJCMqKpqssSISRo' .
            's0PYDTcEQzyyurfX67EJWKtZj6HNwuDMEGJ02iBNZfjUl7r8dIi28bSKhNlsfycXZKY' .
            'RcIjp0+r5RqtR2auo9GQ6veKb61DEAGIqaR+uLLcJVTHCu0w9oXLGbRlGth5eNoj03C' .
            'xXVAH2IfhbNwIDAQABo4IChzCCAoMwDAYDVR0TAQH/BAIwADCCAUgGA1UdIAEB/wSCA' .
            'TwwggE4MIIBNAYJKoRoAYb3IwEBMIIBJTCB3QYIKwYBBQUHAgIwgdAMgc1EZWtsYXJh' .
            'Y2phIHRhIGplc3Qgb8Wbd2lhZGN6ZW5pZW0gd3lkYXdjeSwgxbxlIHRlbiBjZXJ0eWZ' .
            'pa2F0IHpvc3RhxYIgd3lkYW55IGpha28gY2VydHlmaWthdCBrd2FsaWZpa293YW55IH' .
            'pnb2RuaWUgeiB3eW1hZ2FuaWFtaSB1c3Rhd3kgbyBwb2RwaXNpZSBlbGVrdHJvbmlje' .
            'm55bSBvcmF6IHRvd2FyenlzesSFY3ltaSBqZWogcm96cG9yesSFZHplbmlhbWkuMEMG' .
            'CCsGAQUFBwIBFjdodHRwOi8vd3d3Lmtpci5jb20ucGwvY2VydHlmaWthY2phX2tsdWN' .
            '6eS9wb2xpdHlrYS5odG1sMAkGA1UdCQQCMAAwIQYDVR0RBBowGIEWai5wcnpld29yc2' .
            'tpQGdkeW5pYS5wbDAOBgNVHQ8BAf8EBAMCBkAwgZ4GA1UdIwSBljCBk4AU3TGldJXip' .
            'N4oGS3ZYmnBDMFs8gKhd6R1MHMxCzAJBgNVBAYTAlBMMSgwJgYDVQQKDB9LcmFqb3dh' .
            'IEl6YmEgUm96bGljemVuaW93YSBTLkEuMSQwIgYDVQQDDBtDT1BFIFNaQUZJUiAtIEt' .
            '3YWxpZmlrb3dhbnkxFDASBgNVBAUTC05yIHdwaXN1OiA2ggJb9jBIBgNVHR8EQTA/MD' .
            '2gO6A5hjdodHRwOi8vd3d3Lmtpci5jb20ucGwvY2VydHlmaWthY2phX2tsdWN6eS9DU' .
            'kxfT1pLMzIuY3JsMA0GCSqGSIb3DQEBBQUAA4IBAQBYPIqnAreyeql7/opJjcar/qWZ' .
            'y9ruhB2q0lZFsJOhwgMnbQXzp/4vv93YJqcHGAXdHP6EO8FQX47mjo2ZKQmi+cIHJHL' .
            'ONdX/3Im+M17V0iNAh7Z1lOSfTRT+iiwe/F8phcEaD5q2RmvYusR7zXZq/cLL0If0hX' .
            'oPZ/EHQxjN8pxzxiUx6bJAgturnIMEfRNesxwghdr1dkUjOhGLf3kHVzgM6j3VAM7oF' .
            'mMUb5y5s96Bzl10DodWitjOEH0vvnIcsppSxH1C1dCAi0o9f/1y2XuLNhBNHMAyTqpY' .
            'PX8Yvav1c+Z50OMaSXHAnTa20zv8UtiHbaAhwlifCelUMj93S';
        
        try {
            Base64::decode($str, true);
            $this->fail('Strict padding not enforced');
        } catch (Exception ) {
            $this->assertSame(
                Base64::decode($str),
                base64_decode($str)
            );
        }
    }

    public function testIssue22(): void
    {
        // Non-strict: ok
        Base64::decode('00==');

        // Strict: not ok
        $this->expectException(RangeException::class);
        Base64::decode('00==', true);
    }

    public function testDecodeNoPadding(): void
    {
        Base64::decodeNoPadding('0w');
        $this->expectException(InvalidArgumentException::class);
        Base64::decodeNoPadding('0w==');
    }

    /**
     * We need this for PHP before attributes
     * @dataProvider canonicalDataProvider
     */
    #[DataProvider("canonicalDataProvider")]
    public function testNonCanonical(string $input): void
    {
        $w = Base64::encodeUnpadded($input);
        $decoded = Base64::decode($w);
        $this->assertSame($input, $decoded);
        Base64::decode($w, true);

        // Mess with padding:
        $x = $this->increment($w);
        Base64::decode($x);

        // Should throw in strict mode:
        $this->expectException(RangeException::class);
        Base64::decode($x, true);
    }

    protected function getNextChar(string $c): string
    {
        return strtr(
            $c,
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
            'BCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/A'
        );
    }

    public function testUnpadded(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64::encodeUnpadded($random);
            $decoded = Base64::decodeNoPadding($encoded);
            $this->assertSame(
                bin2hex($random),
                bin2hex($decoded)
            );
        }
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            ['ab.d'],
            ['ab:d'],
            ['ab[d'],
            ['ab]d'],
        ];
    }

    /**
     * Detect issue underlying #67 with ext-sodium
     *
     * @return void
     * @throws Exception
     */
    public function testStrictPaddingSodium(): void
    {
        for ($i = 1; $i < 32; ++$i) {
            $random = random_bytes($i);
            $encoded = Base64::encode($random);
            $decoded = Base64::decode($encoded, true);
            $this->assertSame($decoded, $random);
        }
    }

    /**
     * @dataProvider invalidCharactersProvider
     */
    #[DataProvider("invalidCharactersProvider")]
    public function testInvalidCharacters(string $encoded): void
    {
        $this->expectException(RangeException::class);
        Base64::decode($encoded);
    }
}
