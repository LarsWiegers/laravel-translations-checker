<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class MissingTranslationOutputWriter
{

    public static function writeMissingLanguageFile(string $languageWithMissingFile, string $directory, string $fileName): string
    {
        return 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $fileName . ' )';
    }

    public static function writeWithLanguages(string $language, string $keyWithoutFile): string
    {
        return $language . '.' . $keyWithoutFile;
    }

    public static function writeWithoutLanguages(string $language,string  $fileName,string $keyWithoutFile): string
    {
        return $language . '.' . $fileName . '.' . $keyWithoutFile;
    }
}
