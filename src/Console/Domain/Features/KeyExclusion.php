<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Larswiegers\LaravelTranslationsChecker\Console\Domain\Line;

class KeyExclusion
{

    /**
     * @var array|false|string[]
     */
    private static array $excludedKeys = [];

    public static function shouldExclude(Line $line): bool
    {
        return in_array($line->getKey(), self::$excludedKeys);
    }

    /**
     * @param  array<string>  $options
     */
    public static function getExcludedKeys(array $options): void
    {
        if ($options['excludedKeys'] === 'config') {
            self::$excludedKeys = (array) config('translations-checker.excluded_keys', []);
        } elseif ($options['excludedKeys']) {
            self::$excludedKeys = explode(',', $options['excludedKeys']);
        } elseif (empty((array) config('translations-checker.excluded_keys', []))) {
            self::$excludedKeys = (array) config('translations-checker.excluded_keys', []);
        } else {
            self::$excludedKeys = [];
        }
    }
}
