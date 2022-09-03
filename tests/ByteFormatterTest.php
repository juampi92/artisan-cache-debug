<?php

use Juampi92\ArtisanCacheDebug\Support\ByteFormatter;

it('should figure out it\'s type', function (int $bits, string $expected) {
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
