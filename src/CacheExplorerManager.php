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

    public function isUsingRedis(): bool
    {
        return $this->cacheManager->getStore() instanceof RedisStore;
    }

    public function getExplorer(): Explorer
    {
        if (! $this->isUsingRedis()) {
            throw new \Exception('Store not supported.');
        }

        return new RedisExplorer(
            redis: $this->cacheManager->getStore()->connection(),
            prefix: $this->cacheManager->getStore()->getPrefix(),
        );
    }
}
