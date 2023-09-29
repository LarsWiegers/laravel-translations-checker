<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Facades\File as FileFacade;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;

class LanguagesWithMissingFiles
{
    const EXCLUDE_MAC_FILES = ['.DS_Store'];

    /**
     * @var array<string>
     */
    public array $missingFilesTexts = [];

    /**
     * @param File $file
     * @param array<string> $languages
     * @param string $directory
     * @return void
     */
    public function getMissingFilesTexts(File $file, array $languages, string $directory) : void
    {
        $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $file->getBaseName(), $directory);

        foreach ($languagesWithMissingFile as $languageWithMissingFile) {
            if (DirectoryExclusion::shouldExcludeDirectory($languageWithMissingFile)) {
                continue;
            }

            if (in_array($languageWithMissingFile, self::EXCLUDE_MAC_FILES)) {
                continue;
            }

            $this->missingFilesTexts[] = sprintf('The language %s (%s/%s) is missing the file ( %s )',
                $languageWithMissingFile,
                $directory, $languageWithMissingFile,
                $file->getBaseName()
            );
        }
    }

    /**
     * @param array<string> $languages
     * @param string $fileName
     * @param string $baseDirectory
     * @return array<string>
     */
    private function checkIfFileExistsForOtherLanguages(array $languages, string $fileName, string $baseDirectory): array
    {
        $languagesWhereFileIsMissing = [];
        foreach ($languages as $language) {
            if (
                ! FileFacade::exists($baseDirectory.'/'.$language.'/'.$fileName)
                && ! FileFacade::exists($baseDirectory.'/'.$fileName)
            ) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }

    /**
     * @return array<string>
     */
    public function getMissingFileTexts(): array
    {
        return $this->missingFilesTexts;
    }
}
