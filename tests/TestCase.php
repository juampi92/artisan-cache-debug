<?php

namespace Juampi92\ArtisanCacheDebug\Tests;

use Juampi92\ArtisanCacheDebug\ArtisanCacheDebugServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ArtisanCacheDebugServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
