<?php

namespace Juampi92\ArtisanCacheDebug\Support;

class ByteFormatter
{
    public static function fromBits(int $bits, int $precision = 1): string
    {
        $bytes = $bits / 8;

        return match (true) {
            $bits < 8 => "{$bits} bits",
            $bytes / 1024 < 1 => "{$bytes} bytes",
            $bytes / pow(1024, 2) < 1 => sprintf('%s kb', round($bytes / 1024, $precision)),
            $bytes / pow(1024, 3) < 1 => sprintf('%s mb', round($bytes / pow(1024, 2), $precision)),
            default => sprintf('%s gb', round($bytes / pow(1024, 3), $precision)),
        };
    }
}
