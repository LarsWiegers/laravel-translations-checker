<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

class GetLanguages
{
    /**
     * @param string $directory
     * @return array<string>
     */
    public function getLanguages(string $directory): array
    {
        $languages = [];

        if ($handle = opendir($directory)) {
            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir !== '.' && $languageDir !== '..') {
                    $languages[] = str_replace('.json', '', $languageDir);
                }
            }
        }

        closedir($handle);

        return array_filter($languages, static function ($element) {
            return ! in_array($element, config('translations-checker.exclude_languages'));
        });
    }
}
