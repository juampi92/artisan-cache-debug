<?php

namespace Juampi92\ArtisanCacheDebug\Support;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TypeGuesser
{
    /**
     * @param  string  $serialized
     * @return string|class-string
     */
    public static function guess(string $serialized): string
    {
        if (! $serialized) {
            return 'null';
        }

        /** @var array{0: string, 1: string, 2: string} $parts */
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
            'a' => 'array'.self::guessArraySubtype($parts[2]),
            'i' => 'int',
            'd' => 'float',
            'b' => 'bool',
            'O' => self::classType($parts),
            default => 'unknown',
        };
    }

    /**
     * @param  string  $content "{i:0;s:3:"foo";i:1;s:3:"bar";}"
     * @return string
     */
    private static function guessArraySubtype(string $content): string
    {
        if ($content === '{}') {
            // We can't figure the type of an empty array.
            return '';
        }

        [$keyRaw, $valueRaw] = Str::of($content)
            ->substr(1, -1)
            ->explode(';', 3);

        [$key, $firstKey] = explode(':', $keyRaw, 2);
        /** @var array{0: string, 1: string, 2: string} $value */
        $value = explode(':', $valueRaw, 3);

        $valueType = self::letterToType($value[0], $value);

        if ($firstKey == 0) {
            // no key
            return "<{$valueType}>";
        }

        $keyType = self::letterToType($key);

        return "<{$keyType}, {$valueType}>";
    }

    /**
     * @param  string  $letter
     * @param  array{0: string, 1: string, 2: string}|null  $rest
     * @return string
     */
    private static function letterToType(string $letter, ?array $rest = null): string
    {
        return match ($letter) {
            'N' => 'null',
            's' => 'string',
            'a' => 'array',
            'i' => 'int',
            'd' => 'float',
            'b' => 'bool',
            'O' => $rest ?
                substr($rest[2], 1, (int) $rest[1]) ?: 'unknown'
                : 'object',
            default => 'unknown',
        };
    }

    /**
     * Transforms [
     *      '0',
     *      '29',
     *      '"Illuminate\Support\Collection":2:{s:8:"\x00*\x00items";a:3:{i:0;i:1;'
     * ]
     *      to 'Illuminate\Support\Collection<int>'
     *
     * @param  array{0: string, 1: string, 2: string}  $parts
     * @return string
     */
    private static function classType(array $parts): string
    {
        $class = substr($parts[2], 1, (int) $parts[1]);

        if (! $class) {
            return 'unknown';
        }

        if (! in_array($class, [Collection::class, EloquentCollection::class])) {
            return $class;
        }

        $array = Str::of($parts[2])->after('{')->after('{')->before('}');
        $type = self::guessArraySubtype("{{$array}}");

        return "{$class}{$type}";
    }
}
