<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Str;

class DirectoryExclusion
{
    public static $excludedDirectories;

    public static function shouldExcludeDirectory($directoryToCheck): bool
    {
        foreach (self::$excludedDirectories as $excludedDirectory) {
            if (Str::contains($directoryToCheck, $excludedDirectory)) {
                return true;
            }
        }

        return false;
    }

    public static function getExcludedDirectories($options): void
    {
        if ($options['excludedDirectories'] === 'config') {
            self::$excludedDirectories = (array) config('translations-checker.excluded_directories', []);
        } elseif (empty((array) config('translations-checker.excluded_directories', []))) {
            self::$excludedDirectories = (array) config('translations-checker.excluded_directories', []);
        } elseif ($options['excludedDirectories'] === 'none') {
            self::$excludedDirectories = [];
        } elseif ($options['excludedDirectories']) {
            self::$excludedDirectories = explode(',', $options['excludedDirectories']);
        } else {
            self::$excludedDirectories = [];
        }
    }
}
