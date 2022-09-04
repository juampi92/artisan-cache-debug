<?php

namespace Juampi92\ArtisanCacheDebug\Contracts;

use Illuminate\Support\Enumerable;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;

interface Explorer
{
    /**
     * @param  string  $match
     * @return Enumerable<array-key, CacheRecord>
     */
    public function getRecords(string $match = '*'): Enumerable;
}
