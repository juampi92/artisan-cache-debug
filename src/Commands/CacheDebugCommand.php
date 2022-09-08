<?php

namespace Juampi92\ArtisanCacheDebug\Commands;

use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Enumerable;
use Juampi92\ArtisanCacheDebug\CacheExplorerManager;
use Juampi92\ArtisanCacheDebug\DTOs\CacheRecord;
use Juampi92\ArtisanCacheDebug\Support\ByteFormatter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Terminal;

#[AsCommand(name: 'cache:debug')]
class CacheDebugCommand extends Command
{
    public $signature = 'cache:debug
                                    {--key=\* : Filter keys. Use redis filter patterns.}
                                    {--forever : Will only show non-expiring keys.}
                                    {--heavier-than= : Will hide keys lighter than X.}
                                    {--sort-by=size : Will sort the keys by \'size\' or \'key\'.}
                                    {--sort-dir= : Set the sorting direction: \'asc\' or \'desc\'.}
                                    {--with-details : Show the type of every cache record.}';

    public $description = 'Debug cache keys.';

    /**
     * The terminal width resolver callback.
     */
    protected static ?Closure $terminalWidthResolver = null;

    public function handle(CacheExplorerManager $manager): int
    {
        if (! $manager->isUsingRedis()) {
            $this->components->error('This command only supports the \'redis\' cache driver.');

            return self::FAILURE;
        }

        $explorer = $manager->getExplorer();
        $records = $explorer->getRecords($this->getMatch());

        // Filtering
        $records = $this
            ->applyFilters($records)
            ->sortBy($this->getSortBy(), descending: $this->getSortIsDescending());

        if ($records->isEmpty()) {
            $this->components->warn('The cache seems to be empty.');

            return self::SUCCESS;
        }

        $this->output->newLine(1);

        $this->printRecords($records);
        $this->printFooter($records);

        return self::SUCCESS;
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

    /*
     * Access command options.
     */

    private function getMatch(): string
    {
        $match = (string) $this->option('key'); // @phpstan-ignore-line

        if ($match === '\*') {
            return '*';
        }

        return $match;
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

    /*
     * Print methods.
     */

    /**
     * @param  Enumerable<array-key, CacheRecord>  $records
     */
    private function printRecords(Enumerable $records): void
    {
        $terminalWidth = $this->getTerminalWidth();

        $records->each(function (CacheRecord $record) use ($terminalWidth): void {
            $bytes = ByteFormatter::fromBits($record->bits);
            $bytesStyle = $this->getBytesStyle($record->bits);

            $details = $this->option('with-details') ? $record->type : '';

            $dots = str_repeat('.', max(
                $terminalWidth - mb_strlen(" {$record->key} $bytes$details") - 6 - ($details ? 1 : 0), 0
            ));

            $this->output->writeln(
                "<fg=bright-yellow>{$record->key}</> ".
                "<fg=gray>$dots</>".
                ($details ? "<fg=white;options=underscore>{$details}</> " : '').
                "<{$bytesStyle}>{$bytes}</>"
            );
        });
    }

    /**
     * @param  Enumerable<array-key, CacheRecord>  $records
     */
    private function printFooter(Enumerable $records): void
    {
        $terminalWidth = $this->getTerminalWidth();

        $count = $records->count();
        $totalSize = ByteFormatter::fromBits($records->sum('bits'));

        $unstyledFooter = "Showing [{$count}] records. Total size: {$totalSize}";

        $this->newLine();

        $offset = $terminalWidth - mb_strlen($unstyledFooter) - 7;
        $spaces = str_repeat(' ', $offset);

        $this->line($spaces."<fg=blue;options=bold>Showing [{$count}] records. Total size: {$totalSize}</>");
    }

    /*
     * Utilities.
     */

    private function getBytesStyle(int $bits): string
    {
        return match (true) {
            $bits < (8 * 1024) => 'fg=green',
            $bits < (8 * 1024 * 1024) => 'fg=yellow',
            $bits < (8 * 1024 * 1024 * 500) => 'fg=red',
            default => 'fg=bright-red;options=bold',
        };
    }

    private function getTerminalWidth(): int
    {
        return is_null(static::$terminalWidthResolver)
            ? (new Terminal)->getWidth()
            : call_user_func(static::$terminalWidthResolver);
    }
}
