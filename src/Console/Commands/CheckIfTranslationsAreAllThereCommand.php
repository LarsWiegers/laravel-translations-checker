<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CheckIfTranslationsAreAllThereCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check {--directory=} {--excludedDirectories=config}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations are there for all languages.';

    /**
     * @var array
     */
    public array $excludedDirectories;

    /**
     * @var array
     */
    public array $realLines = [];

    const EXCLUDE_MAC_FILES = ['.DS_Store'];

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
        $directory = $this->option('directory') ?: app()->langPath();

        if ($this->option('excludedDirectories') === 'config') {
            $this->excludedDirectories = (array)config('translations-checker.excluded_directories', []);
        } elseif ($this->option('excludedDirectories') === 'none') {
            $this->excludedDirectories = [];
        } elseif ($this->option('excludedDirectories')) {
            $this->excludedDirectories = explode(',', $this->option('excludedDirectories'));
        } else {
            $this->excludedDirectories = [];
        }

        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            return $this::FAILURE;
        }

        $languages = $this->getLanguages($directory);
        $missingFiles = [];

        $path = $directory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {

            if (!File::isDirectory($langFile) && Str::endsWith($langFile, ['.json', '.php'])) {
                $fileName = basename($langFile);
                $languageDir = Str::replace($fileName, '', $langFile);

                $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $fileName, $directory);

                if ($this->isDirInExcludedDirectories($languageDir)) {
                    continue;
                }

                foreach ($languagesWithMissingFile as $languageWithMissingFile) {
                    if ($this->isDirInExcludedDirectories($languageWithMissingFile)) {
                        continue;
                    }

                    if(in_array($languageWithMissingFile, self::EXCLUDE_MAC_FILES)) {
                        continue;
                    }

                    $missingFiles[] = 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $fileName . ' )';
				}
                $this->handleFile($languageDir, $langFile);
            }
        }


        $missing = [];
        foreach ($this->realLines as $key => $line) {

            foreach ($languages as $language) {

                $fileNameWithoutKey = substr($key, 0, strpos($key, "**"));
				$fileKey = basename($fileNameWithoutKey);
                $keyWithoutFile = substr($key, strpos($key, "**") + 2, strlen($key));

				$exists = $this->translationExistsAsJsonOrAsSubDir($directory, $language, $fileKey, $keyWithoutFile);

                if ($this->isDirInExcludedDirectories($language)) {
                    continue;
                }
                if (!$exists) {
                    $fileName = Str::replace(['.php', '.json'], '', $fileKey);

                    foreach($languages as $checkingLanguage) {
                        if(Str::contains($fileName, $checkingLanguage)) {
                            $fileName = str_replace($checkingLanguage, '', $fileName);
                        }
                    }

                    if(in_array($language, self::EXCLUDE_MAC_FILES)) {
                        continue;
                    }

                    if(Str::contains($fileKey, $languages)) {
                        $missing[] = $language . '.' . $keyWithoutFile;
                    }else {
                        $missing[] = $language . '.' . $fileName . '.' . $keyWithoutFile;
                    }

                }
            }
        }

        foreach ($missingFiles as $missingFile) {
            $this->error($missingFile);
        }


        foreach ($missing as $missingTranslation) {
            $this->error('Missing the translation with key: ' . $missingTranslation);
        }

        if (count($missingFiles) === 0 && count($missing) === 0) {
            $this->info('✔ All translations are okay!');
        }

        return count($missing) > 0 || count($missingFiles) > 0 ? $this::FAILURE : $this::SUCCESS;
    }

    public function handleFile($languageDir, $langFile): void
    {
        $excludedLangs = config('translations-checker.exclude_languages');

        if (
            $excludedLangs &&
            (in_array(Str::afterLast($languageDir, '/'), $excludedLangs) ||
                in_array(Str::afterLast(basename($languageDir, '.json'), '/'), $excludedLangs))
        ) {
            return;
        }

        $fileName = basename($langFile);

        if(Str::endsWith($fileName, '.json')) {
            $lines = json_decode(File::get($langFile), true);
        }else {
            $lines = include($langFile);
        }

        if (!is_array($lines)) {
            $this->warn("Skipping file (" . $langFile . ") because it is empty.");
            return;
        }

        foreach ($lines as $index => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $this->realLines[$languageDir . $fileName . '.' . $index . '**' . $index2] = $line2;
                }
            } else {
                $this->realLines[$languageDir  . $fileName . '**' . $index] = $line;
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
    private function getLanguages(string $directory): array
    {
        $languages = [];

        if ($handle = opendir($directory)) {
            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir !== '.' && $languageDir !== '..') {
                    $languages[] = str_replace('.json', '', $languageDir);
                }
            }
        }

        closedir($handle);

        return array_filter($languages, static function ($element) {
            return ! in_array($element, config('translations-checker.exclude_languages'));
        });
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
                !File::exists($baseDirectory . '/' . $language .  '/' . $fileName)
                && !File::exists($baseDirectory . '/' . $fileName)
            ) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }

    private function isDirInExcludedDirectories($directoryToCheck): bool
    {
        foreach($this->excludedDirectories as $excludedDirectory) {
            if(Str::contains($directoryToCheck, $excludedDirectory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $directory
     * @param $language
     * @param string $fileKey
     * @return bool
     */
    public function translationExistsAsJsonOrAsSubDir($directory, $language, string $fileKey, string $keyWithoutFile): bool
    {
        $existsAsSubDirValue = array_key_exists($directory . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $fileKey . '**' . $keyWithoutFile, $this->realLines);

        $fileKeyWithoutLangComponent = explode('.', $fileKey, 2)[1];
        $existsAsJSONValue = array_key_exists($directory . DIRECTORY_SEPARATOR . $language . '.' . $fileKeyWithoutLangComponent . '**' . $keyWithoutFile, $this->realLines);
        return $existsAsSubDirValue || $existsAsJSONValue;
    }
}
