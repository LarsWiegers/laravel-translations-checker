<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class TranslationExistsChecker
{
    /**
     * @param  array<Line>  $realLines
     */
    public function translationExistsAsJsonOrAsSubDir(array $realLines, Line $line, string $language): bool
    {
        return array_key_exists($line->getIDButSwapLanguage($language), $realLines);
    }
}
