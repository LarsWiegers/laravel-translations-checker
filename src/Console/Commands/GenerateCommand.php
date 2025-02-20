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
use function DI\string;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:generate {--inputFile=} {--outputFile=} {--sourceLanguage=} {--targetLanguage=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing translations';
    public $libreHost = 'http://127.0.0.1:5000';

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
        $inputFile = $this->option('inputFile');
        $outputFile = $this->option('outputFile');
        $sourceLanguage = $this->option('sourceLanguage');
        $targetLanguage = $this->option('targetLanguage');


        try {
            $file = $this->loadFile($inputFile);

            foreach($file->getContent() as $key => $value) {
                $translatedValue = $this->translateString($value, $sourceLanguage, $targetLanguage);
                dd($translatedValue);
            }

        }catch(\Exception $exception) {
            dd($exception);
        }
//        $response = Http::post(config('translations-checker.translation_service') . '/translate', [
//            "q" => "Hello!",
//            "source" => "en",
//            "target" => "es"
//        ]);
//        dd($response);

        return 0;
    }

    private function translateString(string $string, string $sourceLanguage, string $targetLanguage): string
    {
        $response = Http::post($this->libreHost . '/translate', [
            'q' => $string,
            'source' => $sourceLanguage,
            'target' => $targetLanguage,
        ]);
        return $response->json()['translatedText'];
    }

    private function loadFile(?string $inputFile): File
    {
        return new File($inputFile);
    }
}
