<?php

use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Support\Facades\Cache;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\Explorers\RedisExplorer;

it('Should resolve different stores', function () {
    // Arrange
    config(['cache.stores.new' => [
        'driver' => 'redis',
        'connection' => 'default',
    ]]);

    $this->artisan(ClearCommand::class);
    $this->artisan(ClearCommand::class, ['store' => 'new']);

    Cache::store()->forever('foo', 'foo');
    Cache::store('new')->forever('bar', 'bar');

    // Act
    /** @var RedisExplorer $defaultExplorer */
    $defaultExplorer = $this->app->make(CacheExplorerManager::class)->getExplorer(null);
    /** @var RedisExplorer $newExplorer */
    $newExplorer = $this->app->make(CacheExplorerManager::class)->getExplorer('new');

    // Assert
    expect($defaultExplorer->getRecords()->first())
        ->key->toBe('foo');
    expect($newExplorer->getRecords()->first())
        ->key->toBe('bar');
});
