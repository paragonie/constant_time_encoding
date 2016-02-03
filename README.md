# Constant-Time Encoding

Based on the work of [Steve "Sc00bz" Thomas](https://github.com/Sc00bz/ConstTimeEncoding), this library aims to offer
character encoding functions that do not leak information about what you are encoding/decoding via processor cache 
misses. Further reading on [cache-timing attacks](http://blog.ircmaxell.com/2014/11/its-all-about-time.html).

Our fork offers the following enchancements:

* `mbstring.func_overload` resistance
* Unit tests
* Composer- and Packagist-ready

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
```

Example output: `hKBHmZuDM9N99tXDoSNrZVoQSpVChxKXjG85cuOuSYI=`