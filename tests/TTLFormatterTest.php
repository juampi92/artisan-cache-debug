<?php

use Juampi92\ArtisanCacheDebug\Support\TTLFormatter;

it('should format the right time', function (int $ttl, string $expected) {
    // Act
    $time = TTLFormatter::format($ttl);

    // Assert
    expect($time)->toBe($expected);
})->with([
    ['target' => -1, 'expected' => 'forever'],
    ['target' => 1, 'expected' => '1s'],
    ['target' => 10, 'expected' => '10s'],
    ['target' => 60, 'expected' => '1 min'],
    ['target' => 65, 'expected' => '1:05 min'],
    // 1 hour, 5 minutes, 5 seconds
    ['target' => 60 * 60 + 5 * 60 + 5, 'expected' => '1:05 h'],
    // 5 days, 2 hours, 5 minutes, 15 seconds
    ['target' => 60 * 60 * 24 * 5 + 60 * 60 * 2 + 5 * 60 + 15, 'expected' => '5 days from now'],
]);
