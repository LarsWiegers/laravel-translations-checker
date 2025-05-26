<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;

class ExtensionExclusion
{
    /**
     * @var array<string>
     */
    public static array $excludedExtensions;

    public static function shouldExcludeExtension(File $file): bool
    {
        foreach (self::$excludedExtensions as $excludedExtension) {
            if (Str::contains($file->getExtension(), $excludedExtension)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string>  $options
     */
    public static function getExcludedExtensions(array $options): void
    {
        if ($options['excludedFileExtensions'] === 'config') {
            self::$excludedExtensions = (array) config('translations-checker.excluded_file_extensions', []);
        } elseif (! empty((array) config('translations-checker.excluded_file_extensions', []))) {
            self::$excludedExtensions = (array) config('translations-checker.excluded_file_extensions', []);
        } elseif ($options['excludedFileExtensions'] === 'none') {
            self::$excludedExtensions = [];
        } elseif ($options['excludedFileExtensions']) {
            self::$excludedExtensions = explode(',', $options['excludedFileExtensions']);
        } else {
            self::$excludedExtensions = [];
        }
    }
}
