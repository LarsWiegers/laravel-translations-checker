<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Line;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\TranslationExistsChecker;

class LanguagesWithMissingKeys
{
    /**
     * @var array<string>
     */
    private array $missingKeysTexts = [];

    private TranslationExistsChecker $translationExistChecker;

    public function __construct()
    {
        $this->translationExistChecker = new TranslationExistsChecker();
    }

    /**
     * @param  array<string, Line>  $realLines
     * @param  array<string>  $languages
     */
    public function getMissingKeysTexts(array $realLines, array $languages): void
    {
        foreach ($realLines as $line) {

            foreach ($languages as $language) {
                if (LanguageExclusion::shouldExcludeLanguage($language)) {
                    continue;
                }

                if (FileExclusion::shouldExclude($language)) {
                    continue;
                }

                $fileNameWithoutKey = $line->fileNameWithoutKey();
                $fileKey = basename($fileNameWithoutKey);
                $keyWithoutFile = $line->keyWithoutFile();

                $exists = $this
                    ->translationExistChecker
                    ->translationExistsAsJsonOrAsSubDir($realLines, $line, $language);

                if ($exists) {
                    continue;
                }

                $file = new File($fileKey);
                $fileName = $file->withoutExtensionAndLanguages($languages);

                if (Str::contains($fileKey, $languages)) {
                    $this->missingKeysTexts[] = $language.'.'.$keyWithoutFile;
                } else {
                    $this->missingKeysTexts[] = $language.'.'.$fileName.'.'.$keyWithoutFile;
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public function getMissingKeys(): array
    {
        return $this->missingKeysTexts;
    }
}
