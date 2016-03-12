# Constant-Time Encoding

[![Build Status](https://travis-ci.org/paragonie/constant_time_encoding.svg?branch=master)](https://travis-ci.org/paragonie/constant_time_encoding)

### Security Warning: Due to how PHP implements `chr()` (and there is no way to work around it), this cannot achieve true cache-timing safety. 

However, if you implement the algorithms in C as part of php-src, you can.

---

Based on the work of [Steve "Sc00bz" Thomas](https://github.com/Sc00bz/ConstTimeEncoding), this library aims to offer
character encoding functions that do not leak information about what you are encoding/decoding via processor cache 
misses. Further reading on [cache-timing attacks](http://blog.ircmaxell.com/2014/11/its-all-about-time.html).

Our fork offers the following enchancements:

* `mbstring.func_overload` resistance
* Unit tests
* Composer- and Packagist-ready
* Base32 encoding

## How to Install

```sh
composer require paragonie/constant_time_encoding
```

## How to Use

```php
use \ParagonIE\ConstantTime\Encoding;

// possibly (if applicable): 
// require 'vendor/autoload.php';

$data = random_bytes(32);
echo Encoding::base64Encode($data), "\n";
echo Encoding::base32Encode($data), "\n";
echo Encoding::hexEncode($data), "\n";
```

Example output:
 
```
1VilPkeVqirlPifk5scbzcTTbMT2clp+Zkyv9VFFasE=
2VMKKPSHSWVCVZJ6E7SONRY3ZXCNG3GE6ZZFU7TGJSX7KUKFNLAQ====
d558a53e4795aa2ae53e27e4e6c71bcdc4d36cc4f6725a7e664caff551456ac1
```
