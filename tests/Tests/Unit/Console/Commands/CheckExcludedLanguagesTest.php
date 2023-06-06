<?php

namespace Larswiegers\LaravelTranslationsChecker\Tests\Tests\Unit\Console\Commands;

use Tests\TestCase;

class CheckExcludedLanguagesTest extends TestCase
{
    private string $languagesDir = 'tests/resources/lang/exclude_langs';
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test excluded languages.
     * @return void
     */
    public function testTheCommandShouldNotProduceAnErrorIfOtherLangsExcluded()
    {
        config()->set('translations-checker.exclude_languages', ['fr', 'ga']);

        $command = $this->artisan('translations:check', [
            '--directory' => $this->languagesDir
        ]);

        $command->assertExitCode(0);
    }

    /**
     * Test without exclusion.
     * @return void
     */
    public function testTheCommandShouldThrowAnErrorIfOtherLangsNotExcluded()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->languagesDir
        ]);

        $command->assertExitCode(1);
    }
}
