<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UnsedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:unsed {--directory=} {--translationDirectory=} {--excludedDirectories=none}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds any translations that are not used.';

    /**
     * @var array
     */
    public array $excludedDirectories;

    /**
     * @var array
     */
    public array $realLines = [];

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
    public function handle(): int
    {
        $directory = $this->option('directory') ?: app()->resourcePath();
        $translationDirectory = $this->option('translationDirectory') ?: app()->langPath();

        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');
            return $this::FAILURE;
        }

        if (!$this->checkIfDirectoryExists($translationDirectory)) {
            $this->error('The passed translation directory (' . $translationDirectory . ') does not exist.');
            return $this::FAILURE;
        }

        $translationsFound = [];

        $path = $directory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {

            if (!File::isDirectory($langFile) && !Str::endsWith($langFile, '.txt')) {
                $lines = file_get_contents($langFile);
                $lines = explode("\n",$lines);
                foreach ($lines as $line) {
                    if (str_contains($line, "__(")) {
                        if (preg_match('/__\((.*?)\)/', $line, $match) == 1) {
                            $foundTranslation = $match[1];
                            $foundTranslation = Str::replace('\'', '', $foundTranslation);
                            $foundTranslation = Str::replace('\"', '', $foundTranslation);
                            $translationsFound[] = $foundTranslation;
                        }
                    }
                }

            }
        }

        $this->getAllTranslations($translationDirectory);


        $translationsNotUsed = [];
        foreach($this->realLines as $translationKey => $translation) {
            if(! in_array($translationKey, $translationsFound)) {
                $translationsNotUsed[] = $translationKey;
            }
        }

        foreach($translationsNotUsed as $translationNotUsed) {
            $this->error('Translation is not used: ' . $translationNotUsed);
        }

        return count($translationsNotUsed) > 0 ? $this::FAILURE : $this::SUCCESS;
    }

    /**
     * @param string $directory
     * @return bool
     */
    private function checkIfDirectoryExists(string $directory): bool
    {
        return File::exists($directory);
    }

    private function getAllTranslations($directory)
    {
        $path = $directory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {

            if (!File::isDirectory($langFile) && !Str::endsWith($langFile, '.txt')) {
                $fileName = basename($langFile);
                $languageDir = Str::replace($fileName, '', $langFile);

                $this->handleFile($languageDir, $langFile);
            }
        }
    }

    public function handleFile($languageDir, $langFile): void
    {
        $fileName = basename($langFile);

        if(Str::endsWith($fileName, '.json')) {
            $lines = json_decode(File::get($langFile), true);
        }else {
            $lines = include($langFile);
        }

        $fileName = Str::replace(".php", "", $fileName);
        $fileName = Str::replace(".json", "", $fileName);

        foreach ($lines as $index => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $this->realLines[$fileName . '.' . $index . '.' . $index2] = $line2;
                }
            } else {
                $this->realLines[$fileName . '.' . $index] = $line;
            }
        }
    }
}
