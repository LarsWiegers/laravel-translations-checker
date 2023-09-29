<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

class LanguagesWithMissingFiles
{
    const EXCLUDE_MAC_FILES = ['.DS_Store'];

    public array $missingFilesTexts = [];
    public function getMissingFilesTexts($file, $languages, $directory) {
        $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $file->getBaseName(), $directory);

        foreach ($languagesWithMissingFile as $languageWithMissingFile) {
            if (DirectoryExclusion::shouldExcludeDirectory($languageWithMissingFile)) {
                continue;
            }

            if(in_array($languageWithMissingFile, self::EXCLUDE_MAC_FILES)) {
                continue;
            }

            $this->missingFilesTexts[] = sprintf("The language %s (%s/%s) is missing the file ( %s )",
                $languageWithMissingFile,
                $directory, $languageWithMissingFile,
                $file->getBaseName()
            );
        }
    }
    /**
     * @param $languages
     * @param $fileName
     * @param $baseDirectory
     * @return array
     */
    private function checkIfFileExistsForOtherLanguages($languages, $fileName, $baseDirectory): array
    {
        $languagesWhereFileIsMissing = [];
        foreach ($languages as $language) {
            if (
                !FileFacade::exists($baseDirectory . '/' . $language .  '/' . $fileName)
                && !FileFacade::exists($baseDirectory . '/' . $fileName)
            ) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }

    public function getMissingFileTexts(): array
    {
        return $this->missingFilesTexts;
    }
}
