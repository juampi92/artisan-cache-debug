<?php

namespace Juampi92\ArtisanCacheDebug;

use Illuminate\Cache\RedisStore;
use Illuminate\Contracts\Cache\Repository;
use Juampi92\ArtisanCacheDebug\Contracts\Explorer;
use Juampi92\ArtisanCacheDebug\Explorers\RedisExplorer;

class CacheExplorerManager
{
    public function __construct(
        private readonly Repository $cacheManager,
    ) {
    }

    public function isUsingRedis(): bool
    {
        return $this->cacheManager->getStore() instanceof RedisStore;
    }

    public function getExplorer(): Explorer
    {
        $store = $this->cacheManager->getStore();

        if (! $store instanceof RedisStore) {
            throw new \Exception('Store not supported.');
        }

        return new RedisExplorer(
            redis: $store->connection(),
            prefix: $store->getPrefix(),
        );
    }
}
