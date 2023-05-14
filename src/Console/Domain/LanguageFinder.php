<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class LanguageFinder
{

    /**
     * @param string $directory
     * @return array
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

        return $languages;
    }
}
