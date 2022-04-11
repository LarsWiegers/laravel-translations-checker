<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

final class UnsedCommandTest extends TestCase
{

    public $zeroErrorsBasicDir = "tests/resources/UnsedResources/zero_errors/app";
    public $zeroErrorsBasicDirLang = "tests/resources/UnsedResources/zero_errors/lang";

    public $oneErrorBasicDir = "tests/resources/UnsedResources/one_error/app";
    public $oneErrorBasicDirLang = "tests/resources/UnsedResources/one_error/lang";

    public $multipleErrorBasicDir = "tests/resources/UnsedResources/multiple/app";
    public $multipleBasicDirLang = "tests/resources/UnsedResources/multiple/lang";

    public $baseLaravelErrorBasicDir = "tests/resources/UnsedResources/laravel_project";
    public $baseLaravelBasicDirLang = "tests/resources/UnsedResources/laravel_project/lang";

    public function test_if_translation_exists_it_is_fine()
    {
        $command = $this->artisan('translations:unsed', [
            '--directory' => $this->zeroErrorsBasicDir,
            '--translationDirectory' => $this->zeroErrorsBasicDirLang,
        ]);

        $command->doesntExpectOutput('Translation is not used: test.test_key');
    }

    public function test_it_fails_for_basic_laravel_project()
    {
        $command = $this->artisan('translations:unsed', [
            '--directory' => $this->baseLaravelErrorBasicDir,
            '--translationDirectory' => $this->baseLaravelBasicDirLang,
        ]);

        $command->assertFailed();
    }

    public function test_if_translation_shows_warning_if_key_is_not_used()
    {
        $command = $this->artisan('translations:unsed', [
            '--directory' => $this->oneErrorBasicDir,
            '--translationDirectory' => $this->oneErrorBasicDirLang,
        ]);

        $command->expectsOutput('Translation is not used: test.key_not_used');
    }

    public function test_if_translation_shows_warning_if_multiple_keys_are_missing()
    {
        $command = $this->artisan('translations:unsed', [
            '--directory' => $this->multipleErrorBasicDir,
            '--translationDirectory' => $this->multipleBasicDirLang,
        ]);

        $command->expectsOutput('Translation is not used: test.key_not_used');
        $command->expectsOutput('Translation is not used: test.other_not_used');
    }
}
