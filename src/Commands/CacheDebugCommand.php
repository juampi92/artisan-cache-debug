<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Enumerable;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;
use Juampi92\ArtisanCacheDebug\Support\ByteFormatter;

class CacheDebugCommand extends Command
{
    public $signature = 'cache:debug
                                    {--key=\* : Filter keys. Use redis filter patterns.}
                                    {--forever : Will only show non-expiring keys}
                                    {--heavier-than= : Will hide keys lighter than X}';

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
        $match = (string) $this->option('key');

        if ($match === '\*') {
            return '*';
        }

        return $match;
    }

    /**
     * @param  Enumerable<array-key, CacheRecord>  $records
     */
    private function printRecords(Enumerable $records): void
    {
        // Print all records.
        $records->each(function (CacheRecord $record) {
            $this->components->twoColumnDetail(
                $record->key,
                ByteFormatter::fromBits($record->bits),
            );
        });
    }

    /**
     * @param  Enumerable<array-key, CacheRecord>  $records
     * @return Enumerable<array-key, CacheRecord>
     */
    private function applyFilters(Enumerable $records): Enumerable
    {
        $foreverFilter = (bool) $this->option('forever');
        $heavierThanFilter = $this->option('heavier-than') ?
            ByteFormatter::fromString((string) $this->option('heavier-than')) // @phpstan-ignore-line
            : null;

        return $records
            ->when(
                $foreverFilter,
                /**
                 * @param  Enumerable<array-key, CacheRecord>  $records
                 * @return Enumerable<array-key, CacheRecord>
                 */
                function (Enumerable $records): Enumerable {
                    return $records->filter(fn (CacheRecord $record) => $record->ttl === -1);
                }
            )
            ->when(
                ! is_null($heavierThanFilter),
                /**
                 * @param  Enumerable<array-key, CacheRecord>  $records
                 * @return Enumerable<array-key, CacheRecord>
                 */
                function (Enumerable $records) use ($heavierThanFilter): Enumerable {
                    return $records->filter(fn (CacheRecord $record) => $record->bits > $heavierThanFilter);
                }
            );
    }
}
