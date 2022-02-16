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
    protected $signature = 'translations:check {--directory=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations are there for all languages.';

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
        if($this->option('directory')) {
            $directory = $this->option('directory');
        }else {
            $directory = app()->langPath();
        }

        if(! $this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            return 1;
        };

        $realLines = [];
        $languages = [];
        $missingFiles = [];

        $languages = $this->getLanguages($directory);

        if ($handle = opendir($directory)) {

            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir != "." && $languageDir != "..") {

                    if ($handleLang = opendir($directory . '/' . $languageDir)) {

                        while (false !== ($langFile = readdir($handleLang))) {
                            if ($langFile != "." && $langFile != ".." && !Str::endsWith($langFile, '.txt')) {


                                $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $langFile, $directory);

                                foreach($languagesWithMissingFile as $languageWithMissingFile) {
                                    $missingFiles[] = 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $langFile. ' )';
                                }

                                $lines = include($directory . '/' . $languageDir . '/' . $langFile);


                                $fileName = str_replace(".php", "", $langFile);

                                foreach ($lines as $index => $line) {
                                    if (is_array($line)) {
                                        foreach ($line as $index2 => $line2) {
                                            $realLines[$languageDir . '.' . $fileName . '.' . $index . '.' . $index2] = $line2;
                                        }
                                    } else {
                                        $realLines[$languageDir . '.' . $fileName . '.' . $index] = $line;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            closedir($handleLang);
            closedir($handle);

            $missing = [];
            foreach($realLines as $key => $line) {

                $withoutLocale = strstr($key, '.', false);

                foreach($languages as $language) {
                    $exists = array_key_exists($language . $withoutLocale, $realLines);

                    if(!$exists) {
                        $missing[] = $language . $withoutLocale;
                    }
                }
            }
        }

        foreach($missingFiles as $missingFile) {
            $this->error($missingFile);
        }


        foreach($missing as $missingTranslation) {
            $this->error('Missing the translation with key: ' . $missingTranslation);
        }
        dd($missingFiles, $missing, count($missing) > 0 ? 1 : 0);
        return count($missing) > 0 ? 1 : 0;
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
    private function getLanguages(string $directory): array
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

    private function checkIfFileExistsForOtherLanguages($languages, $fileName, $baseDirectory): array
    {
        $languagesWhereFileIsMissing = [];

        foreach($languages as $language) {
            dump(File::exists($baseDirectory . '/' . $language . '/' . $fileName), $languages, $fileName, $baseDirectory);
            if(! File::exists($baseDirectory . '/' . $language . '/' . $fileName)) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }
}
