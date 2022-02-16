<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckIfTranslationsAreAllThereCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check';

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
        $directory = (app()->langPath());

        $realLines = [];

        if ($handle = opendir($directory)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != "vendor") {
                    if ($handleLang = opendir(lang_path($entry))) {
                        while (false !== ($langFile = readdir($handleLang))) {
                            if ($langFile != "." && $langFile != "..") {
                                $lines = include(app()->langPath($entry . '/' . $langFile));

                                $fileName = str_replace(".php", "", $langFile);

                                foreach ($lines as $index => $line) {
                                    if (is_array($line)) {
                                        foreach ($line as $index2 => $line2) {
                                            $realLines[$entry . '.' . $fileName . '.' . $index . '.' . $index2] = $line2;
                                        }
                                    } else {
                                        $realLines[$entry . '.' . $fileName . '.' . $index] = $line;
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
                if(Str::startsWith($key, 'en.')) {
                    $withoutLocale = Str::replaceFirst('en.', '', $key);
                    $exists = array_key_exists('nl.' . $withoutLocale, $realLines);
                    if(!$exists) {
                       $missing[] = 'nl.' . $withoutLocale;
                    }
                }

                if(Str::startsWith($key, 'nl.')) {
                    $withoutLocale = Str::replaceFirst('nl.', '', $key);
                    $exists = array_key_exists('en.' . $withoutLocale, $realLines);
                    if(!$exists) {
                        $missing[] = 'en.' . $withoutLocale;
                    }
                }
            }
        }
        foreach($missing as $missingTranslation) {
            $this->error('Missing the translation with key: ' . $missingTranslation);
        }

        return count($missing) > 0 ? 1 : 0;
    }
}
