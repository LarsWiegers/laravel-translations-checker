<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as FileFacade;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\DirectoryExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetLanguages;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\KeyExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingFiles;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingKeys;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Line;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CheckIfTranslationsAreAllThereCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check {--directory=} {--excludedDirectories=config} {--excludedKeys=config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations are there for all languages.';

    /**
     * @var array<string, Line>
     */
    public array $realLines = [];

    private LanguagesWithMissingFiles $missingFileChecker;

    private LanguagesWithMissingKeys $missingKeyChecker;

    private GetLanguages $getLanguages;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->missingFileChecker = (new LanguagesWithMissingFiles);
        $this->missingKeyChecker = (new LanguagesWithMissingKeys);
        $this->getLanguages = new GetLanguages();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $topDirectory = $this->option('directory') ?: app()->langPath();
        if (! FileFacade::exists($topDirectory)) {
            $this->error('The passed directory ('.$topDirectory.') does not exist.');

            return $this::FAILURE;
        }

        DirectoryExclusion::getExcludedDirectories($this->options());
        KeyExclusion::getExcludedKeys($this->options());

        $languages = $this->getLanguages->getLanguages($topDirectory);

        $path = $topDirectory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {
            $file = new File($langFile);
            /**
             * Makes sure we don't check any stray files left in the directory
             */
            if (! $file->isLangFile()) {
                continue;
            }
            /**
             * Exclusion feature
             */
            if (DirectoryExclusion::shouldExcludeDirectory($file->getLanguageDir())) {
                continue;
            }

            /**
             * We check if all files exists for all languages
             */
            $this->missingFileChecker->getMissingFilesTexts($file, $languages, $topDirectory);

            /**
             * Write to memory, so we can check if all translations are there
             *  Unsure why this rewrite to realLines is needed. but it is.
             */
            //            dump($file->handle($topDirectory, $languages));
            foreach ($file->handle($topDirectory, $languages) as $key => $line) {
                $this->realLines[$key] = $line;
            }
        }

        /**
         * We check if all keys exists for all languages
         */
        $this->missingKeyChecker->getMissingKeysTexts($this->realLines, $languages);

        foreach ($this->missingFileChecker->getMissingFileTexts() as $missingFile) {
            $this->error($missingFile);
        }

        foreach ($this->missingKeyChecker->getMissingKeys() as $missingTranslation) {
            $this->error('Missing the translation with key: '.$missingTranslation);
        }

        if (count($this->missingFileChecker->getMissingFileTexts()) === 0 &&
            count($this->missingKeyChecker->getMissingKeys()) === 0
        ) {
            $this->info('✔ All translations are okay!');
        }

        return
            count($this->missingKeyChecker->getMissingKeys()) > 0 ||
            count($this->missingFileChecker->missingFilesTexts) > 0
                ? $this::FAILURE
                : $this::SUCCESS;
    }
}
