<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Str;

class LanguageExclusion
{
    public static function shouldExcludeLanguage($languageDir):bool
    {
        $excludedLanguages = config('translations-checker.exclude_languages');

        return $excludedLanguages &&
        (
            in_array(Str::afterLast($languageDir, '/'), $excludedLanguages) ||
            in_array(Str::afterLast(basename($languageDir, '.json'), '/'), $excludedLanguages));
    }
}
