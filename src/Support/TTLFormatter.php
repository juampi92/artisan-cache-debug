<?php

namespace Juampi92\ArtisanCacheDebug\Support;

use Illuminate\Support\Carbon;
use const STR_PAD_LEFT;

class TTLFormatter
{
    public static function format(int $ttl): string
    {
        if ($ttl === -1) {
            return 'forever';
        }

        return match (true) {
            $ttl < 60 => "{$ttl}s",
            $ttl === 60 => '1 min',
            $ttl < 60 * 60 => self::formatMinutes($ttl),
            $ttl === 60 * 60 => '1h',
            $ttl < 60 * 60 * 24 => self::formatHours($ttl),
            default => Carbon::now()->addSeconds($ttl)->diffForHumans(),
        };
    }

    private static function formatMinutes(int $ttl): string
    {
        $minutes = (int) floor($ttl / 60);
        $seconds = str_pad(
            (string) ($ttl - ($minutes * 60)),
            2,
            '0',
            STR_PAD_LEFT
        );

        return "{$minutes}:{$seconds} min";
    }

    private static function formatHours(int $ttl): string
    {
        $ttlMin = $ttl / 60;
        $hours = (int) floor($ttlMin / 60);
        $minutes = str_pad(
            (string) floor($ttlMin - ($hours * 60)),
            2,
            '0',
            STR_PAD_LEFT
        );

        return "{$hours}:{$minutes} h";
    }
}
