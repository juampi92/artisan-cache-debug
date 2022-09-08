<?php

namespace Juampi92\ArtisanCacheDebug\Support;

use Exception;
use Illuminate\Support\Str;

class ByteFormatter
{
    private const UNITS = [
        'bits' => 1,
        'bit' => 1,
        'b' => 8,
        'bytes' => 8,
        'byte' => 8,
        'kb' => 8 * 1024,
        'mb' => 8 * 1024 * 1024,
        'gb' => 8 * 1024 * 1024 * 1024,
    ];

    public static function fromBits(int $bits, int $precision = 1): string
    {
        $bytes = ceil($bits / 8);

        return match (true) {
            $bits < 8 => "{$bits} bits",
            $bytes / 1024 < 1 => "{$bytes} bytes",
            $bytes / pow(1024, 2) < 1 => sprintf('%s kb', round($bytes / 1024, $precision)),
            $bytes / pow(1024, 3) < 1 => sprintf('%s mb', round($bytes / pow(1024, 2), $precision)),
            default => sprintf('%s gb', round($bytes / pow(1024, 3), $precision)),
        };
    }

    public static function fromString(string $value): int
    {
        $power = self::getUnitPower($value);
        $modifier = floatval($value);

        return (int) ($power * $modifier);
    }

    private static function getUnitPower(string $value): int
    {
        $unit = Str::of($value)->lower()->replaceMatches('/[0-9\.]+/', '')->trim()->value();

        return collect(self::UNITS)
            ->first(
                fn (int $power, string $powerUnit): bool => $powerUnit === $unit
            ) ?: throw new Exception("The unit '{$unit}' is not supported. Try bits, b, kb, mb");
    }
}
