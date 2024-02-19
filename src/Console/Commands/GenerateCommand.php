<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Http;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\DirectoryExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetLanguages;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\KeyExclusion;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingFiles;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguagesWithMissingKeys;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\File;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Line;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Jefs42\LibreTranslate;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing translations';

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
     */
    public function handle(): int
    {

        $translator = new LibreTranslate("https://libretranslate.com/translate", 5000);
//        $response = Http::post(config('translations-checker.translation_service') . '/translate', [
//            "q" => "Hello!",
//            "source" => "en",
//            "target" => "es"
//        ]);
//        dd($response);
        dd($translator);

        return 0;
    }
}
