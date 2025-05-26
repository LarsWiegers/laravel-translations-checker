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

    /**
     * @return array<string, mixed>
     */
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
     * @param  array<string>  $languages
     * @return array<string, Line>
     */
    public function handle(string $topDirectory, array $languages): array
    {
        $languageExclusion = new LanguageExclusion();
        if ($languageExclusion->shouldExcludeLanguage($this->getLanguageDir())) {
            return [];
        }

        $fileName = $this->getBaseName();
        $lines = $this->getContent();

        $realLines = [];

        $language = Str::betweenFirst(Str::replace($topDirectory, '', $this->getLanguageDir()), '/', '/');
        if (str_contains($fileName, '.json') && in_array(str_replace('.json', '', $fileName), $languages)) {
            $language = str_replace('.json', '', $fileName);
        }

        foreach ($lines as $key => $line) {
            $line = $this->createLine($language, $fileName, $key, $line);
            if ($line != null) {
                $realLines[$line->getID()] = $line;
            }
        }

        return $realLines;
    }

    public function getWithoutExtension(): string
    {
        return Str::replace(['.php', '.json'], '', $this->fileName);
    }

    public function getExtension(): string
    {
        return Str::contains($this->fileName, '.php') ? 'php' : 'json';
    }

    /**
     * @param  array<string>  $languages
     */
    public function withoutExtensionAndLanguages(array $languages): string
    {
        $fileName = $this->getWithoutExtension();
        foreach ($languages as $checkingLanguage) {
            if (Str::contains($fileName, $checkingLanguage)) {
                $fileName = str_replace($checkingLanguage, '', $fileName);
            }
        }

        return $fileName;
    }

    public function replaceLanguage(string $language, string $base): string
    {
        $withoutBase = Str::replace($base, '', $this->fileName);
        $langPart = explode('/', $withoutBase)[1];
        $withoutBase = Str::replace($langPart, $language, $withoutBase);

        return $base.$withoutBase;
    }

    public function createLine(string $language, string $fileName, string $key, mixed $line): ?Line
    {
        if (is_array($line)) {
            foreach ($line as $subKey => $subLine) {
                $this->createLine($language, $fileName, $subKey, $subLine);
            }
        }

        if (is_array($line)) {
            return null;
        }

        return new Line(
            Str::replaceLast('/'.$language, '', $this->getLanguageDir()),
            $fileName,
            $key,
            $line,
            str_contains($fileName, '.json'),
            str_contains($fileName, '.php'),
            $language,
        );
    }
}
