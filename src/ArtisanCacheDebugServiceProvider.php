<?php

namespace Juampi92\ArtisanCacheDebug;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Juampi92\ArtisanCacheDebug\Commands\ArtisanCacheDebugCommand;

class ArtisanCacheDebugServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('artisan-cache-debug')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_artisan-cache-debug_table')
            ->hasCommand(ArtisanCacheDebugCommand::class);
    }
}
