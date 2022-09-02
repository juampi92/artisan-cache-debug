<?php

namespace Juampi92\ArtisanCacheDebug\DTOs;

class CacheRecord
{
    public function __construct(
        public readonly string $key,
        public readonly string $type,
        public readonly int $bytes,
        public readonly ?int $ttl,
    )
    {
    }
}
