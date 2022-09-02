<?php

namespace Juampi92\ArtisanCacheDebug;

use Juampi92\ArtisanCacheDebug\Commands\CacheDebugCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ArtisanCacheDebugServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('artisan-cache-debug')
            ->hasCommand(CacheDebugCommand::class);
    }
}
