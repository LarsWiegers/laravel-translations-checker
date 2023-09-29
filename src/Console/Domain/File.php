<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\LanguageExclusion;

class File
{
    public string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function isLangFile(): bool
    {
        return ! FileFacade::isDirectory($this->fileName) && Str::endsWith($this->fileName, ['.json', '.php']);
    }

    public function getBaseName(): string
    {
        return basename($this->fileName);
    }

    public function getLanguageDir(): string
    {
        $fileName = basename($this->fileName);

        return Str::replace($fileName, '', $this->fileName);
    }

    public function getContent(): array
    {
        if (Str::endsWith($this->fileName, '.json')) {
            $lines = json_decode(FileFacade::get($this->fileName), true);
        } else {
            $lines = include $this->fileName;
        }

        return $lines;
    }

    /**
     * @return array<Line>
     */
    public function handle($topDirectory, $languages): array
    {
        $languageExclusion = new LanguageExclusion();
        if ($languageExclusion->shouldExcludeLanguage($this->getLanguageDir())) {
            return [];
        }

        $fileName = $this->getBaseName();
        $lines = $this->getContent();

        $realLines = [];

        $language = Str::replace('/', '', Str::replace($topDirectory, '', $this->getLanguageDir()));
        if (str_contains($fileName, '.json') && in_array(str_replace('.json', '', $fileName), $languages)) {
            $language = str_replace('.json', '', $fileName);
        }

        foreach ($lines as $key => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $line = new Line(
                        Str::replaceLast('/'.$language.'/', '', $this->getLanguageDir()),
                        $fileName,
                        $key.'**'.$index2,
                        $line2,
                        str_contains($fileName, '.json'),
                        str_contains($fileName, '.php'),
                        $language,
                    );
                    $realLines[$line->getID()] = $line;
                }
            } else {

                $line = new Line(
                    Str::replaceLast('/'.$language.'/', '', $this->getLanguageDir()),
                    $fileName,
                    $key,
                    $line,
                    str_contains($fileName, '.json'),
                    str_contains($fileName, '.php'),
                    $language,
                );
                $realLines[$line->getID()] = $line;
            }
        }

        return $realLines;
    }

    public function getWithoutExtension(): string
    {
        return Str::replace(['.php', '.json'], '', $this->fileName);
    }

    public function withoutExtensionAndLanguages(array $languages)
    {
        $fileName = $this->getWithoutExtension();
        foreach ($languages as $checkingLanguage) {
            if (Str::contains($fileName, $checkingLanguage)) {
                $fileName = str_replace($checkingLanguage, '', $fileName);
            }
        }

        return $fileName;
    }
}
