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
     * @param  array<string>  $languages
     */
    public function getMissingFilesTexts(File $file, array $languages, string $directory): void
    {
        $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $file, $directory);

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
     * @param File $file
     * @param string $baseDirectory
     * @return array<string>
     */
    private function checkIfFileExistsForOtherLanguages(array $languages, File $file, string $baseDirectory): array
    {
        $languagesWhereFileIsMissing = [];
        foreach ($languages as $language) {
            if (
                ! FileFacade::exists($file->replaceLanguage($language, $baseDirectory)) &&
                ! FileFacade::exists($file->replaceLanguage($language, $baseDirectory).'.json')
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
