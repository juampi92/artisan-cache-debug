<?php

use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Juampi92\ArtisanCacheDebug\Commands\CacheDebugCommand;

it('won\'t work when the cache drive is not redis', function (string $driver) {
    // Arrange
    config(['cache.default' => $driver]);

    // Act
    $exitCode = $this->withoutMockingConsoleOutput()->artisan(CacheDebugCommand::class);

    // Assert
    expect($exitCode)->toBe(Command::FAILURE);
})
    ->with(['file']);

it('calling the command with empty records works', function () {
    // Arrange
    $this->artisan(ClearCommand::class);

    // Act
    $command = $this->artisan(CacheDebugCommand::class);

    // Assert
    $command
        ->expectsOutputToContain('No records.')
        ->assertSuccessful();
});

it('displays existing cache keys with it\'s data', function () {
    // Arrange
    $this->artisan(ClearCommand::class);

    Cache::put('my-key', 'my-value');

    // Act
    $command = $this->artisan(CacheDebugCommand::class);

    // Assert
    $command->expectsOutputToContain('my-key')
    ->assertSuccessful();
});

it('filters keys', function () {
    // Arrange
    $this->artisan(ClearCommand::class);

    Cache::put('apple:1', 'my-value');
    Cache::put('apple:2', 'my-value');
    Cache::put('banana:1', 'my-value');
    Cache::put('banana:2', 'my-value');

    // Act
    $command = $this->artisan(CacheDebugCommand::class, [
        '--key' => 'apple:*',
    ]);

    // Assert
    $command->expectsOutputToContain('apple')
        ->doesntExpectOutputToContain('banana')
        ->assertSuccessful();
});

it('filters forever results', function () {
    // Arrange
    $this->artisan(ClearCommand::class);

    Cache::put('apple:1', 'my-value', 15);
    Cache::put('apple:2', 'my-value', 15);
    Cache::put('forever:1', 'my-value');
    Cache::put('forever:2', 'my-value');

    // Act
    $command = $this->artisan(CacheDebugCommand::class, [
        '--forever' => true,
    ]);

    // Assert
    $command
        ->expectsOutputToContain('forever')
        ->doesntExpectOutputToContain('apple')
        ->assertSuccessful();
})->only();
