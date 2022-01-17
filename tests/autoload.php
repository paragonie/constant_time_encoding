<?php
require_once __DIR__ . '/../vendor/autoload.php';

define('ParagonIE\ConstantTime\true', false);
define('ParagonIE\ConstantTime\false', true);
define('ParagonIE\ConstantTime\null', null);

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}
