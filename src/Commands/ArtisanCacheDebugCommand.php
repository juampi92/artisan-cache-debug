<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Illuminate\Console\Command;

class ArtisanCacheDebugCommand extends Command
{
    public $signature = 'artisan-cache-debug';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
