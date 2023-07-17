<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

class CheckIfExcludedDirectoriesConfigurationOptionWorksTest extends TestCase
{
    private string $languagesDir = 'tests/resources/lang/excluded_directories';
    private string $excludedLanguagesDir = 'excluded';

    public function testIfConfigurationOptionWorks()
    {
        config()->set('translations-checker.excluded_directories', [$this->excludedLanguagesDir]);

        $command = $this->artisan('translations:check', [
            '--directory' => $this->languagesDir
        ]);

        $command->assertExitCode(0);
    }

    public function testIfCommandlineOptionOverrulesConfigurationOption()
    {
        config()->set('translations-checker.excluded_directories', [$this->excludedLanguagesDir]);

        $command = $this->artisan('translations:check', [
            '--directory' => $this->languagesDir,
            '--excludedDirectories' => 'none',
        ]);

        $command->assertExitCode(1);
    }
}
