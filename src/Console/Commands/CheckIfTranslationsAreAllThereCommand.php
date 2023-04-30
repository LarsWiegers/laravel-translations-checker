<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
    protected $signature = 'translations:check {--directory=} {--excludedDirectories=none} {--excludedKeys=none}';

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
    private Collection $excludedKeys;

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
        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            return $this::FAILURE;
        }

        $this->excludedDirectories = $this->getExcludedDirectories();

        $this->excludedKeys = $this->getExcludedKeys();


        $languages = $this->getLanguages($directory);
        $missingFiles = [];

        $path = $directory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {
            if (File::isDirectory($langFile) || !Str::endsWith($langFile, ['.json', '.php'])) {
                continue;
            }

            $fileName = basename($langFile);
            $languageDir = Str::replace($fileName, '', $langFile);
            if ($this->isDirInExcludedDirectories($languageDir)) {
                continue;
            }

            $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $fileName, $directory);

            foreach ($languagesWithMissingFile as $languageWithMissingFile) {
                if ($this->isDirInExcludedDirectories($languageWithMissingFile)) {
                    continue;
                }

                $missingFiles[] = 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $fileName . ' )';
            }
            $this->handleFile($languageDir, $langFile);
        }


        $missing = [];
        foreach ($this->realLines as $key => $line) {

            foreach ($languages as $language) {

                $fileNameWithoutKey = substr($key, 0, strpos($key, "**"));
				$fileKey = basename($fileNameWithoutKey);
                $keyWithoutFile = substr($key, strpos($key, "**") + 2, strlen($key));

				$exists = $this->translationExistsAsJsonOrAsSubDir($directory, $language, $fileKey, $keyWithoutFile);

                $keyToCheck = str_replace('**', '.',
                    substr(
                        $key, strpos($key, "$$") + 2, strlen($key)
                    )
                );

                if(str_starts_with($keyToCheck, '.')) {
                    $keyToCheck = str_replace('.', '', $keyToCheck);
                }

                if ($this->isDirInExcludedDirectories($language) ||
                    $exists ||
                    $this->excludedKeys->contains(
                        $keyToCheck
                    )
                ) {
                    continue;
                }

                $fileName = Str::replace(['.php', '.json'], '', $fileKey);

                foreach($languages as $checkingLanguage) {
                    if(Str::contains($fileName, $checkingLanguage)) {
                        $fileName = str_replace($checkingLanguage, '', $fileName);
                    }
                }

                if(Str::contains($fileKey, $languages)) {
                    $missing[] = $language . '.' . $keyWithoutFile;
                }else {
                    $missing[] = $language . '.' . str_replace('$$', '', $fileName) . '.' . $keyWithoutFile;
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
                    $this->realLines[$languageDir . $fileName . '$$' . $index . '**' . $index2] = $line2;
                }
            } else {
                $this->realLines[$languageDir  . $fileName . '$$**' . $index] = $line;
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

        return $languages;
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

    /**
     * @return array
     */
    public function getExcludedDirectories(): array
    {
        if ($this->option('excludedDirectories') === 'none') {
            return [];
        } elseif ($this->option('excludedDirectories')) {
            return explode(',', $this->option('excludedDirectories'));
        } else {
            return [];
        }
    }

    private function getExcludedKeys(): Collection
    {
        if ($this->option('excludedKeys') === 'none') {
            return collect(config('translation-checker.excluded_keys'));
        } elseif ($this->option('excludedKeys')) {
            return collect(explode(',', $this->option('excludedKeys')));
        } else {
            return collect();
        }
    }
}
