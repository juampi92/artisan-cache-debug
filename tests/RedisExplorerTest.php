<?php

use Illuminate\Cache\Console\ClearCommand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\Explorers\RedisExplorer;

it('Should get CacheRecords from redis', function () {
    // Arrange
    $this->artisan(ClearCommand::class);

    Cache::forever('1-first', 'a');
    Cache::put('2-second', [1, 2, 3], Carbon::now()->addDay());
    Cache::put('3-third', 1, Carbon::now()->addHour());
    Cache::put('4-fourth', 1.5, Carbon::now()->addMinute());

    // Act
    /** @var RedisExplorer $explorer */
    $explorer = $this->app->make(CacheExplorerManager::class)->getExplorer();
    $records = $explorer->getRecords()->sortBy('key')->values()->all();

    // Assert
    expect($records)
        ->toHaveCount(4)
        // First:
        ->and($records[0])
        ->key->toBe('1-first')
        ->type->toBe('string')
        ->bits->toBe(28)
        ->ttl->toBe(-1)
        // Second:
        ->and($records[1])
        ->key->toBe('2-second')
        ->type->toBe('array')
        ->ttl->toBe(60 * 60 * 24)
        // Third:
        ->and($records[2])
        ->key->toBe('3-third')
        ->type->toBe('int')
        ->bits->toBe(3)
        ->ttl->toBe(60 * 60)
        // Fourth:
        ->and($records[3])
        ->key->toBe('4-fourth')
        ->type->toBe('float')
        ->bits->toBe(11)
        ->ttl->toBe(60);
});
