<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileHandler
{
    public function handleFile($languageDir, $langFile): array
    {
        $fileName = basename($langFile);
        $lines = $this->getContent($langFile);

        $newLines = [];

        foreach ($lines as $index => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $newLines[$languageDir . $fileName . '.' . $index . '**' . $index2] = $line2;
                }
            } else {
                $newLines[$languageDir . $fileName . '**' . $index] = $line;
            }
        }

        return $newLines;
    }

    public function getContent($langFile)
    {
        $fileName = basename($langFile);

        if (Str::endsWith($fileName, '.json')) {
            $lines = json_decode(File::get($langFile), true);
        } else {
            $lines = include($langFile);
        }

        return $lines;
    }
}
