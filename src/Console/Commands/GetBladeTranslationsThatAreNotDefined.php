<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as FileFacade;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\DirectoryExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetBladeTranslations;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetLanguages;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\KeyExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingFiles;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingKeys;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Line;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GetBladeTranslationsThatAreNotDefined extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:blade {--topDirectory=} {--langDirectory=} {{--bladeDirectory=}} {--excludedDirectories=config} {--excludedKeys=config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations that are used in blade files are defined.';

    /**
     * @var array<string, Line>
     */
    public array $realLines = [];

    private GetLanguages $getLanguages;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->getLanguages = new GetLanguages();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $langDirectory = $this->option('langDirectory') ?: app()->langPath();
        $bladeDirectory = $this->option('bladeDirectory') ?: app()->resourcePath();
        $topDirectory = $this->option('topDirectory') ?: app()->basePath();

        if (! FileFacade::exists($langDirectory)) {
            $this->error('The passed directory ('.$langDirectory.') does not exist.');

            return $this::FAILURE;
        }

        DirectoryExclusion::getExcludedDirectories($this->options());
        KeyExclusion::getExcludedKeys($this->options());

        $languages = $this->getLanguages->getLanguages($langDirectory);
        $path = $langDirectory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {
            $file = new File($langFile);
            /**
             * Makes sure we don't check any stray files left in the directory
             */
            if (!$file->isLangFile()) {
                continue;
            }
            /**
             * Exclusion feature
             */
            if (DirectoryExclusion::shouldExcludeDirectory($file->getLanguageDir())) {
                continue;
            }

            /**
             * Write to memory, so we can check if all translations are there
             *  Unsure why this rewrite to realLines is needed. but it is.
             */
            //            dump($file->handle($langDirectory, $languages));
            foreach ($file->handle($topDirectory, $languages) as $key => $line) {
                $this->realLines[$key] = $line;
            }
        }

        $blade = new GetBladeTranslations($bladeDirectory);
        $bladeTranslations = $blade->get();
        $bladeTranslationsFound = [];
        foreach($this->realLines as $line) {
            foreach ($line->getPossibleUseCases() as $useCase) {
                foreach($bladeTranslations as $foundTranslation) {
                    if($foundTranslation === $useCase) {
                        $line->setIsUsedInBlade(true);
                        $bladeTranslationsFound[] = $foundTranslation;
                    }
                }
            }
        }

        $foundInBladeButNotDefined = array_diff($bladeTranslations, $bladeTranslationsFound);
        dump($foundInBladeButNotDefined);

        return 0;
    }
}
