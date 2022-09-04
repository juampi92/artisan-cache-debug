<?php

use Illuminate\Support\Collection;
use Juampi92\ArtisanCacheDebug\Support\TypeGuesser;

it('should figure out it\'s type from serialized', function (mixed $target, string $expected) {
    // Arrange
    $serialized = serialize($target);

    // Act
    $type = TypeGuesser::guess($serialized);

    // Assert
    expect($type)->toBe($expected);
})->with([
    ['target' => null, 'expected' => 'null'],
    ['target' => 'I am a string', 'expected' => 'string'],
    ['target' => [1, 2, 3], 'expected' => 'array'],
    ['target' => 1, 'expected' => 'int'],
    ['target' => 1.5, 'expected' => 'float'],
    ['target' => true, 'expected' => 'bool'],
    ['target' => collect([1, 2, 3]), 'expected' => Collection::class],
    ['target' => (object) ['a' => 1], 'expected' => 'stdClass'],
]);

it('should figure out it\'s type from raw cache', function (mixed $target, string $expected) {
    // Act
    $type = TypeGuesser::guess($target);

    // Assert
    expect($type)->toBe($expected);
})->with([
    ['target' => '1', 'expected' => 'int'],
    ['target' => 1, 'expected' => 'int'],
    ['target' => '1.5', 'expected' => 'float'],
    ['target' => 1.5, 'expected' => 'float'],
]);
