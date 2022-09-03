<?php

namespace Juampi92\ArtisanCacheDebug\Explorers;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Juampi92\ArtisanCacheDebug\Contracts\Explorer;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;
use Juampi92\ArtisanCacheDebug\Support\TypeGuesser;
use Redis;

class RedisExplorer implements Explorer
{
    private string $databasePrefix;

    public function __construct(
        private readonly Connection $redis,
        private readonly string $prefix,
    ) {
        $this->databasePrefix = $this->redis->client()->getOption(Redis::OPT_PREFIX);
    }

    public function getRecords(): Collection
    {
        return $this->getAllKeys()
            // Fix keys
            ->map(fn ($key) => Str::replaceFirst($this->databasePrefix, '', $key))
            // Format
            ->map(fn ($key) => $this->getRecordInformation($key))
            // Filtering
            // Sorting
;
    }

    /**
     * @return Collection<array-key, string>
     */
    private function getAllKeys(): Collection
    {
        return collect($this->redis->keys('*'));
    }

    private function getRecordInformation(string $key): CacheRecord
    {
        $value = $this->redis->get($key);
        $ttl = $this->redis->ttl($key);

        $prefixlessKey = Str::replaceFirst($this->prefix, '', $key);

        return new CacheRecord(
            key: $prefixlessKey,
            type: TypeGuesser::guess($value),
            bits: $this->redis->bitcount($key),
            ttl: $ttl,
        );
    }
}
