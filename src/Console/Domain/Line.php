<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

class Line
{
    private string $key;

    private string $value;

    private string $directory;

    private string $fileName;

    private bool $isJson;

    private bool $isPHP;

    private string $language;

    public function __construct(string $directory, string $fileName, string $key, string $value, bool $isJson, bool $isPHP, string $language)
    {
        $this->directory = $directory;
        $this->fileName = $fileName;
        $this->key = $key;
        $this->value = $value;
        $this->isJson = $isJson;
        $this->isPHP = $isPHP;
        $this->language = $language;

    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function fileNameWithoutKey(): string
    {
        return str_replace($this->key, '', $this->fileName);
    }

    public function keyWithoutFile()
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
            return $this->directory.DIRECTORY_SEPARATOR.$this->language.DIRECTORY_SEPARATOR.$this->getFileName().'**'.$this->getKey();
        } elseif ($this->isJson) {
            return $this->directory.$this->language.'**'.$this->getKey();
        }

        return $this->directory.$this->fileName.'**'.$this->key;
    }

    public function getIDButSwapLanguage(string $language): string
    {
        if ($this->isPHP) {
            return $this->directory.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$this->getFileName().'**'.$this->getKey();
        } elseif ($this->isJson) {
            return $this->directory.$language.'**'.$this->getKey();
        }

        return $this->directory.$this->fileName.'**'.$this->key;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
