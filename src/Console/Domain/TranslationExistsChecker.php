<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class TranslationExistsChecker
{
    /**
     * @param array<Line> $realLines
     * @param Line $line
     * @param string $language
     * @return bool
     */
    public function translationExistsAsJsonOrAsSubDir(array $realLines, Line $line, string $language): bool
    {
        //        dump($line->getIDButSwapLanguage($language), array_keys($realLines), array_key_exists($line->getIDButSwapLanguage($language), $realLines));
        return array_key_exists($line->getIDButSwapLanguage($language), $realLines);
    }
}
