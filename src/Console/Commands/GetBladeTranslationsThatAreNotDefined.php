<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Exception;
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
    protected $signature = 'translations:blade {--topDirectory=} {--langDirectory=} {--bladeDirectory=} {--excludedDirectories=config} {--excludedKeys=config}';

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
        try {
            $topDirectory = $this->getTopDirectory();
            $langDirectory = $this->getLangDirectory($topDirectory);
            $bladeDirectory = $this->getBladeDirectory($topDirectory);
        }catch(Exception $exception) {
            $this->error($exception->getMessage());
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

        foreach ($foundInBladeButNotDefined as $missingTranslation) {
            $this->error('The translation: "'.$missingTranslation . '" is used in blade but not defined in the language files.');
        }

        return count($foundInBladeButNotDefined) > 0 ? $this::FAILURE : $this::SUCCESS;
    }

    /**
     * @param mixed $topDirectory
     * @return array|bool|string|null
     * @throws Exception
     */
    public function getBladeDirectory(mixed $topDirectory): string|array|bool|null
    {
        if($this->option('langDirectory') === 'config') {
            $bladeDirectory = config('translations-checker.blade_directory');
        }elseif ($this->option('bladeDirectory')) {
            $bladeDirectory = $this->option('bladeDirectory');
        } elseif ($topDirectory !== '') {
            $bladeDirectory = $topDirectory . '/resources/views';
        } else {
            $bladeDirectory = app()->resourcePath();
        }

        if (! FileFacade::exists($topDirectory)) {
            throw new Exception('The passed blade directory ('.$bladeDirectory.') does not exist.');
        }

        return $bladeDirectory;
    }

    /**
     * @param bool|array|string $topDirectory
     * @return array|bool|string|null
     * @throws Exception
     */
    public function getLangDirectory(bool|array|string $topDirectory): string|array|bool|null
    {
        if($this->option('langDirectory') === 'config') {
            $langDirectory = config('translations-checker.lang_directory');
        }else if ($this->option('langDirectory')) {
            $langDirectory = $this->option('langDirectory');
        }else if ($topDirectory !== '') {
            $langDirectory = $topDirectory . '/lang';
        } else {
            $langDirectory = app()->langPath();
        }

        if (! FileFacade::exists($topDirectory)) {
            throw new Exception('The passed lang directory ('.$langDirectory.') does not exist.');
        }

        return $langDirectory;
    }

    /**
     * @return bool|array|string
     * @throws Exception
     */
    private function getTopDirectory(): bool|array|string
    {
        $topDirectory = $this->option('topDirectory') ?: app()->basePath();

        if (! FileFacade::exists($topDirectory)) {
            throw new Exception('The passed top directory ('.$topDirectory.') does not exist.');
        }

        return $topDirectory;
    }
}
