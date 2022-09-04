<?php

namespace Juampi92\ArtisanCacheDebug\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface Explorer
{
    public function getRecords(string $match = '*'): Collection|LazyCollection;
}
