<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class TranslationExistsChecker
{
    public function translationExistsAsJsonOrAsSubDir($realLines, Line $line, string $language): bool
    {
        //        dump($line->getIDButSwapLanguage($language), array_keys($realLines), array_key_exists($line->getIDButSwapLanguage($language), $realLines));
        return array_key_exists($line->getIDButSwapLanguage($language), $realLines);
    }
}
