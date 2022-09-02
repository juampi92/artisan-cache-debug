<?php

namespace Juampi92\ArtisanCacheDebug\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Juampi92\ArtisanCacheDebug\ArtisanCacheDebug
 */
class ArtisanCacheDebug extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Juampi92\ArtisanCacheDebug\ArtisanCacheDebug::class;
    }
}
