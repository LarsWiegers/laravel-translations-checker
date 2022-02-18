<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckIfTranslationsAreAllThereCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check {--directory=} {--excludedDirectories=none}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations are there for all languages.';

    /**
     * @var array
     */
    public $excludedDirectories;

    /**
     * @var array
     */
    public $realLines = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('directory')) {
            $directory = $this->option('directory');
        } else {
            $directory = app()->langPath();
        }

        if ($this->option('excludedDirectories') == 'none') {
            $this->excludedDirectories = [];
        } elseif ($this->option('excludedDirectories')) {
            $this->excludedDirectories = explode(',', $this->option('excludedDirectories'));
        } else {
            $this->excludedDirectories = [];
        }

        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            return 1;
        };

        $this->realLines = [];
        $missingFiles = [];

        $languages = $this->getLanguages($directory);

        if ($handle = opendir($directory)) {

            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir != "." && $languageDir != "..") {

                    if ($this->isDirInExcludedDirectories($languageDir)) {
                        continue;
                    }

                    if ($handleLang = opendir($directory . '/' . $languageDir)) {

                        while (false !== ($langFile = readdir($handleLang))) {
                            if ($langFile != "." && $langFile != ".." && !Str::endsWith($langFile, '.txt')) {


                                $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $langFile, $directory);

                                foreach ($languagesWithMissingFile as $languageWithMissingFile) {
                                    if ($this->isDirInExcludedDirectories($languageDir)) {
                                        continue;
                                    }

                                    $missingFiles[] = 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $langFile . ' )';
                                }

                                $this->handleFile($directory, $languageDir, $langFile);
                            }
                        }
                    }

                    closedir($handleLang);
                }
            }
        }

        closedir($handle);

        $missing = [];
        foreach ($this->realLines as $key => $line) {

            $withoutLocale = strstr($key, '.', false);

            foreach ($languages as $language) {
                $exists = array_key_exists($language . $withoutLocale, $this->realLines);

                if ($this->isDirInExcludedDirectories($language)) {
                    continue;
                }

                if (!$exists) {
                    $missing[] = $language . $withoutLocale;
                }
            }
        }

        foreach ($missingFiles as $missingFile) {
            $this->error($missingFile);
        }


        foreach ($missing as $missingTranslation) {
            $this->error('Missing the translation with key: ' . $missingTranslation);
        }

        if (count($missingFiles) == 0 && count($missing) == 0) {
            $this->info('✔ All translations are okay!');
        }

        return count($missing) > 0 ? 1 : 0;
    }

    public function handleFile($directory, $languageDir, $langFile)
    {
        $lines = include($directory . '/' . $languageDir . '/' . $langFile);

        $fileName = str_replace(".php", "", $langFile);

        foreach ($lines as $index => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $this->realLines[$languageDir . '.' . $fileName . '.' . $index . '.' . $index2] = $line2;
                }
            } else {
                $this->realLines[$languageDir . '.' . $fileName . '.' . $index] = $line;
            }
        }
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function checkIfDirectoryExists(string $directory): bool
    {
        return File::exists($directory);
    }

    /**
     * @param string $directory
     * @return array
     */
    private function getLanguages(string $directory)
    {
        $languages = [];

        if ($handle = opendir($directory)) {
            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir != "." && $languageDir != "..") {
                    $languages[] = $languageDir;
                }
            }
        }

        closedir($handle);

        return $languages;
    }

    /**
     * @param $languages
     * @param $fileName
     * @param $baseDirectory
     * @return array
     */
    private function checkIfFileExistsForOtherLanguages($languages, $fileName, $baseDirectory)
    {
        $languagesWhereFileIsMissing = [];

        foreach ($languages as $language) {

            if (!File::exists($baseDirectory . '/' . $language . '/' . $fileName)) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }

    private function isDirInExcludedDirectories($directoryToCheck): bool
    {
        foreach ($this->excludedDirectories as $excludedDirectory) {
            if ($directoryToCheck == $excludedDirectory) {
                return true;
            }
        }

        return false;
    }
}
