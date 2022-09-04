<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Illuminate\Console\Command;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;

class CacheDebugCommand extends Command
{
    public $signature = 'cache:debug
                                    {--key=\* : Filter keys. Use redis filter patterns.}';

    public $description = 'Debug cache.';

    public function handle(CacheExplorerManager $manager): int
    {
        if (! $manager->isUsingRedis()) {
            $this->error('This command only supports redis cache.');

            return self::FAILURE;
        }

        $explorer = $manager->getExplorer();
        $records = $explorer->getRecords($this->getMatch());

        if ($records->isEmpty()) {
            $this->error('No records.');

            return self::SUCCESS;
        }

        $this->output->newLine(2);

        $records->each(function (CacheRecord $record) {
            $this->components->twoColumnDetail(
                $record->key,
                $record->bits,
            );
        });

        return self::SUCCESS;
    }

    private function getMatch(): string
    {
        $match = $this->option('key');

        if ($match === '\*') {
            return '*';
        }

        return $match;
    }
}
