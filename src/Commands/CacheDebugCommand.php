<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Enumerable;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;
use Juampi92\ArtisanCacheDebug\Support\ByteFormatter;

class CacheDebugCommand extends Command
{
    public $signature = 'cache:debug
                                    {--key=\* : Filter keys. Use redis filter patterns.}
                                    {--forever : Will only show non-expiring keys.}
                                    {--heavier-than= : Will hide keys lighter than X.}
                                    {--sort-by=size : Will sort the keys by \'size\' or \'key\'.}
                                    {--sort-dir= : Set the sorting direction: \'asc\' or \'desc\'.}
                                    {--with-details : Show the type of every cache record.}';

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
        $records = $this
            ->applyFilters($records)
            ->sortBy($this->getSortBy(), descending: $this->getSortIsDescending());

        if ($records->isEmpty()) {
            $this->error('No records.');

            return self::SUCCESS;
        }

        $this->output->newLine(2);

        $this->printRecords($records);
        $this->printFooter($records);

        return self::SUCCESS;
    }

    private function getMatch(): string
    {
        $match = (string) $this->option('key'); // @phpstan-ignore-line

        if ($match === '\*') {
            return '*';
        }

        return $match;
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

    private function getSortBy(): string
    {
        $sortBy = (string) $this->option('sort-by') ?: 'size';  // @phpstan-ignore-line

        return match ($sortBy) {
            'size' => 'bits',
            'key' => 'key',
            default => throw new Exception("It's not possible to sort by '{$sortBy}'"),
        };
    }

    private function getSortIsDescending(): bool
    {
        $sortDir = $this->option('sort-dir');

        return match ($sortDir) {
            'asc' => false,
            'desc' => true,
            default => match ($this->getSortBy()) {
                'bits' => true,
                'key' => false,
                default => false,
            }
        };
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
            $this->printRecordDetails($record);
        });
    }

    private function printRecordDetails(CacheRecord $record): void
    {
        if (!$this->option('with-details')) {
            return;
        }

        $this->line("   ↳ <options=bold>Type:</> <options=underscore>{$record->type}</>");
    }

    /**
     * @param  Enumerable<CacheRecord>  $records
     * @return void
     */
    private function printFooter(Enumerable $records): void
    {
        $totalSize = ByteFormatter::fromBits($records->sum('bits'));

        $this->newLine();
        $this->info("Total keys: {$records->count()}. Total size: {$totalSize}");
    }
}
