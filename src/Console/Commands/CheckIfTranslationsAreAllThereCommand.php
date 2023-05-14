<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\DirectoryNotFoundException;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\LanguageFinder;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\MissingTranslationOutputWriter;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\OptionsHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
    public array $excludedDirectories;

    /**
     * @var array
     */
    public array $realLines = [];
    private LanguageFinder $languageFinder;
    private FileHandler $fileHandler;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->languageFinder = new LanguageFinder();
        $this->fileHandler = new FileHandler($this);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            ['directory' => $directory] = $this->handleOptions();
        }catch(DirectoryNotFoundException $directoryNotFoundException) {
            return $this::FAILURE;
        }

        $languages = $this->languageFinder->getLanguages($directory);
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

                    $missingFiles[] = MissingTranslationOutputWriter::writeMissingLanguageFile($languageWithMissingFile, $directory, $fileName);
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

                    if(Str::contains($fileKey, $languages)) {
                        $missing[] = MissingTranslationOutputWriter::writeWithLanguages($language, $keyWithoutFile);
                    }else {
                        $missing[] = MissingTranslationOutputWriter::writeWithoutLanguages($language,$fileName,$keyWithoutFile);
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
        $fileName = basename($langFile);

        if (Str::endsWith($fileName, '.json')) {
            $lines = json_decode(File::get($langFile), true);
        } else {
            $lines = include($langFile);
        }

        if (!is_array($lines)) {
            $this->warn("Skipping file (" . $langFile . ") because it is empty.");
            return;
        }

        $this->realLines = array_merge(
            $this->realLines,
            $this->fileHandler->handleFile($languageDir, $langFile)
        );
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
     * @throws DirectoryNotFoundException
     */
    private function handleOptions(): array
    {
        $directory = $this->option('directory') ?: app()->langPath();

        if ($this->option('excludedDirectories') === 'none') {
            $this->excludedDirectories = [];
        } elseif ($this->option('excludedDirectories')) {
            $this->excludedDirectories = explode(',', $this->option('excludedDirectories'));
        } else {
            $this->excludedDirectories = [];
        }

        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            throw new DirectoryNotFoundException($directory);
        }

        return [
            'directory' => $directory,
        ];
    }
}
