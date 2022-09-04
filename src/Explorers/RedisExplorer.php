<?php

namespace Juampi92\ArtisanCacheDebug\Explorers;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\LazyCollection;
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

    public function getRecords($match = '*', $pageSize = 35): LazyCollection
    {
        return $this->getKeys($match, $pageSize)
            // Fix keys
            ->map(fn ($key) => Str::replaceFirst($this->databasePrefix, '', $key))
            // Format
            ->map(fn ($key) => $this->getRecordInformation($key));
    }

    private function getKeys($match, $pageSize): LazyCollection
    {
        return new LazyCollection(function () use ($pageSize, $match) {
            $cursor = null;

            do {
                /** @var array{0: int, 1: array<array-key, string>}|false|null $items */
                $items = $this->redis->scan(
                    $cursor,
                    [
                        'match' => "{$this->databasePrefix}{$this->prefix}{$match}",
                        'count' => $pageSize,
                    ]
                );

                if (! $items) {
                    break;
                }

                [$cursor, $results] = $items;

                foreach ($results as $row) {
                    yield $row;
                }
            } while ($cursor > 0);
        });
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
