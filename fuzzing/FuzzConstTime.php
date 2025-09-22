<?php

use ParagonIE\ConstantTime\{Base32,
    Base32Hex,
    Base64,
    Base64UrlSafe,
    Base64DotSlash,
    Base64DotSlashOrdered,
    EncoderInterface,
    Hex};

require __DIR__ . '/../vendor/autoload.php';

/** @var PhpFuzzer\Config $config */
$config->setTarget(function(string $input) {
    static $targets = [
        Base32::class,
        Base32Hex::class,
        Base64::class,
        Base64UrlSafe::class,
        Base64DotSlash::class,
        Base64DotSlashOrdered::class,
        Hex::class,
    ];

    /** @var class-string<EncoderInterface> $target */
    $target = $targets[array_rand($targets)];
    $out = $target::decode($input, true);
    $re = $target::encode($out);
    if ($re !== $input) {
        throw new Exception('invalid');
    }
});
