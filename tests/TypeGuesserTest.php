<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Juampi92\ArtisanCacheDebug\Support\TypeGuesser;

it('should figure out its type from serialized', function (mixed $target, string $expected) {
    // Arrange
    $serialized = serialize($target);

    // Act
    $type = TypeGuesser::guess($serialized);

    // Assert
    expect($type)->toBe($expected);
})->with([
    ['target' => null, 'expected' => 'null'],
    ['target' => 'I am a string', 'expected' => 'string'],
    ['target' => 1, 'expected' => 'int'],
    ['target' => 1.5, 'expected' => 'float'],
    ['target' => true, 'expected' => 'bool'],
    ['target' => (object) ['a' => 1], 'expected' => 'stdClass'],
]);

it('should figure out its type from raw cache', function (mixed $target, string $expected) {
    // Act
    $type = TypeGuesser::guess($target);

    // Assert
    expect($type)->toBe($expected);
})->with([
    ['target' => '1', 'expected' => 'int'],
    ['target' => 1, 'expected' => 'int'],
    ['target' => '1.5', 'expected' => 'float'],
    ['target' => 1.5, 'expected' => 'float'],
    ['target' => 'my:string', 'expected' => 'unknown'],
    ['target' => 'O:w:"Hi Mark"', 'expected' => 'unknown'],
]);

it('should figure out its array type', function (mixed $target, string $expected) {
    // Arrange
    $serialized = serialize($target);

    // Act
    $type = TypeGuesser::guess($serialized);

    // Assert
    expect($type)->toBe($expected);
})->with([
    ['target' => new EloquentCollection([new Fluent(), 1, 2, 3]), 'expected' => EloquentCollection::class.'<'.Fluent::class.'>'],
    ['target' => collect([1, 2, 3]), 'expected' => Collection::class.'<int>'],
    ['target' => collect(['foo' => new Fluent(), 'bar' => new Fluent()]), 'expected' => Collection::class.'<string, '.Fluent::class.'>'],
    ['target' => [1, 2, 3], 'expected' => 'array<int>'],
    ['target' => ['a' => 'foo', 'b' => 'bar'], 'expected' => 'array<string, string>'],
    ['target' => ['foo', 'bar'], 'expected' => 'array<string>'],
    ['target' => [true, false], 'expected' => 'array<bool>'],
    ['target' => [1.5, 2.1], 'expected' => 'array<float>'],
    ['target' => collect([[1, 2]]), 'expected' => Collection::class.'<array>'],
    ['target' => [[1, 2]], 'expected' => 'array<array>'],
    ['target' => [null], 'expected' => 'array<null>'],
]);
