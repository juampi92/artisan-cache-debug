<?php

namespace Juampi92\ArtisanCacheDebug\Support;

class TypeGuesser
{
    /**
     * @param  string  $serialized
     * @return string|class-string
     */
    public static function guess(string $serialized): string
    {
        $parts = explode(':', $serialized, 3);
        $primitive = $parts[0];

        // Numbers are not serialized:
        if (is_numeric($serialized)) {
            return match (true) {
                (string) intval($serialized) === $serialized => 'int',
                default => 'float',
            };
        }

        return match ($primitive) {
            'N;' => 'null',
            's' => 'string',
            'a' => 'array',
            'i' => 'int',
            'd' => 'float',
            'b' => 'bool',
            'O' => substr($parts[2], 1, $parts[1]),
            default => dd($serialized) and 'unknown',
        };
    }
}
