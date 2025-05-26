<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

use Illuminate\Support\Str;

class Line
{
    private string $key;

    private string $value;

    private string $directory;

    private string $fileName;

    private bool $isJson;

    private bool $isPHP;

    private string $language;

    public bool $isUsedInBlade = false;

    public function __construct(string $directory, string $fileName, string $key, string $value, bool $isJson, bool $isPHP, string $language)
    {
        $this->directory = Str::replaceLast('/', '', $directory);
        $this->fileName = $fileName;
        $this->key = $key;
        $this->value = $value;
        $this->isJson = $isJson;
        $this->isPHP = $isPHP;
        $this->language = $language;

    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function fileNameWithoutKey(): string
    {
        return str_replace($this->key, '', $this->fileName);
    }

    public function keyWithoutFile(): string
    {
        return str_replace($this->fileName, '', $this->key);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getID(): string
    {
        if ($this->isPHP) {
            return $this->directory.DIRECTORY_SEPARATOR.$this->language.DIRECTORY_SEPARATOR.$this->getFileName().'.'.$this->getKey();
        } elseif ($this->isJson) {
            return $this->directory.DIRECTORY_SEPARATOR.$this->language.'.'.$this->getKey();
        }

        return $this->directory.DIRECTORY_SEPARATOR.$this->fileName.'.'.$this->key;
    }

    public function getIDButSwapLanguage(string $language): string
    {
        if ($this->isPHP) {
            return $this->directory.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$this->getFileName().'.'.$this->getKey();
        } elseif ($this->isJson) {
            return $this->directory.DIRECTORY_SEPARATOR.$language.'.'.$this->getKey();
        }

        return $this->directory.DIRECTORY_SEPARATOR.$this->fileName.'.'.$this->key;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    private function getFileNameWithoutExtension(): string
    {
        return Str::replace(['.php', '.json'], '', $this->fileName);
    }

    /**
     * @return array<string>
     */
    public function getPossibleUseCases(): array
    {
        $useCases = [];
        if ($this->isPHP) {
            $useCases[] = $this->getFileNameWithoutExtension().'.'.$this->getKey();
        } elseif ($this->isJson) {
            $useCases[] = $this->language.'.'.$this->getKey();
        }

        $useCases[] = $this->directory.DIRECTORY_SEPARATOR.$this->fileName.'.'.$this->key;

        return $useCases;
    }

    public function setIsUsedInBlade(bool $bool): void
    {
        $this->isUsedInBlade = $bool;
    }

    public function getIsUsedInBlade(): bool
    {
        return $this->isUsedInBlade;
    }
}
