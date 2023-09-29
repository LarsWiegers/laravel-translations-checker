<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\TranslationExistsChecker;

class LanguagesWithMissingKeys
{
    private array $missingKeysTexts = [];
    private TranslationExistsChecker $translationExistChecker;

    public function __construct() {
        $this->translationExistChecker = new TranslationExistsChecker();
    }

    public function getMissingKeysTexts($realLines, $languages, $topDirectory) {
        foreach ($realLines as $line) {

            foreach ($languages as $language) {
                if (LanguageExclusion::shouldExcludeLanguage($language)) {
                    continue;
                }

                if(FileExclusion::shouldExclude($language)) {
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


                if(Str::contains($fileKey, $languages)) {
                    $this->missingKeysTexts[] = $language . '.' . $keyWithoutFile;
                }else {
                    $this->missingKeysTexts[] = $language . '.' . $fileName . '.' . $keyWithoutFile;
                }
            }
        }
    }

    public function getMissingKeys(): array
    {
        return $this->missingKeysTexts;
    }
}
