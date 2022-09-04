<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;

class CacheDebugCommand extends Command
{
    public $signature = 'cache:debug
                                    {--key=\* : Filter keys. Use redis filter patterns.}
                                    {--forever : Will only show non-expiring keys}';

    public $description = 'Debug cache.';

    public function handle(CacheExplorerManager $manager): int
    {
        if (! $manager->isUsingRedis()) {
            $this->error('This command only supports redis cache.');

            return self::FAILURE;
        }

        $explorer = $manager->getExplorer();
        $records = $explorer->getRecords($this->getMatch());

        // Filtering
        $records = $this->applyFilters($records);

        if ($records->isEmpty()) {
            $this->error('No records.');

            return self::SUCCESS;
        }

        $this->output->newLine(2);

        $this->printRecords($records);

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

    private function printRecords(Collection|LazyCollection $records): void
    {
        // Print all records.
        $records->each(function (CacheRecord $record) {
            $this->components->twoColumnDetail(
                $record->key,
                $record->bits,
            );
        });
    }

    private function applyFilters(Collection|LazyCollection $records): Collection|LazyCollection
    {
        $foreverFilter = $this->option('forever');

        return $records
            ->when(
                $foreverFilter,
                function (Collection|LazyCollection $records) {
                    return $records->filter(fn (CacheRecord $record) => $record->ttl === -1);
                }
            );
    }
}
