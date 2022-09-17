<?php

namespace Juampi92\ArtisanCacheDebug;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\RedisStore;
use Juampi92\ArtisanCacheDebug\Contracts\Explorer;
use Juampi92\ArtisanCacheDebug\Explorers\RedisExplorer;

class CacheExplorerManager
{
    public function __construct(
        private readonly CacheManager $cacheManager,
    ) {
    }

    public function isUsingRedis(?string $store = null): bool
    {
        return $this->cacheManager->store($store)->getStore() instanceof RedisStore;
    }

    public function getExplorer(?string $store = null): Explorer
    {
        $store = $this->cacheManager->store($store)->getStore();

        if (! $store instanceof RedisStore) {
            throw new \Exception('Store not supported.');
        }

        return new RedisExplorer(
            redis: $store->connection(),
            prefix: $store->getPrefix(),
        );
    }
}
