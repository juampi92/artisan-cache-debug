<?php

use Juampi92\ArtisanCacheDebug\Support\ByteFormatter;

it('should output a readable string from bits', function (int $bits, string $expected) {
    // Act
    $size = ByteFormatter::fromBits($bits);

    // Assert
    expect($size)->toBe($expected);
})->with([
    ['target' => 3, 'expected' => '3 bits'],
    ['target' => 8, 'expected' => '1 bytes'],
    ['target' => 8 * 2, 'expected' => '2 bytes'],
    ['target' => 8 * 1023, 'expected' => '1023 bytes'],
    ['target' => 8 * 1024, 'expected' => '1 kb'],
    ['target' => 8 * 1024 * 1.5, 'expected' => '1.5 kb'],
    ['target' => 8 * 1024 * 2, 'expected' => '2 kb'],
    ['target' => 8 * 1024 * 1024, 'expected' => '1 mb'],
    ['target' => 8 * 1024 * 1024 * 2, 'expected' => '2 mb'],
]);

it('should output bits from readable string', function (string $string, int $expected) {
    // Act
    $bits = ByteFormatter::fromString($string);

    // Assert
    expect($bits)->toBe($expected);
})->with([
    ['target' => '3 bits', 'expected' => 3],
    ['target' => '3bits', 'expected' => 3],
    ['target' => '1 bytes', 'expected' => 8],
    ['target' => '1 byte', 'expected' => 8],
    ['target' => '2byte', 'expected' => 8 * 2],
    ['target' => '2bytes', 'expected' => 8 * 2],
    ['target' => '2b', 'expected' => 8 * 2],
    ['target' => '2 b', 'expected' => 8 * 2],
    ['target' => '0.5 kb', 'expected' => 8 * 512],
    ['target' => '0.5kb', 'expected' => 8 * 512],
    ['target' => '1 kb', 'expected' => 8 * 1024],
    ['target' => '1.5 kb', 'expected' => 8 * 1024 * 1.5],
    ['target' => '2 kb', 'expected' => 8 * 1024 * 2],
    ['target' => '1 mb', 'expected' => 8 * 1024 * 1024],
    ['target' => '2 mb', 'expected' => 8 * 1024 * 1024 * 2],
    ['target' => '2mb', 'expected' => 8 * 1024 * 1024 * 2],
]);
