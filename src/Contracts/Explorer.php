<?php

namespace Juampi92\ArtisanCacheDebug\Contracts;

use Illuminate\Support\Collection;

interface Explorer
{
    public function getRecords(): Collection;
}
