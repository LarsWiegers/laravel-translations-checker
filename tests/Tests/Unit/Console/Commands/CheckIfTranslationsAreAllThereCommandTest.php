<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class CheckIfTranslationsAreAllThereCommandTest extends TestCase
{
    const multipleDirectoriesDir = 'tests/resources/lang/multiple_directory_levels/';
    public $basicDir = 'tests/resources/lang/basic/';

    const basicDir = "tests/resources/lang/basic/";
    const jsonDir = "tests/resources/lang/json/";
    const multipleLangs  = "tests/resources/lang/multi_langs/";

    #[DataProvider('one_missing_key_provider')]
    public function test_it_returns_errors_if_one_key_is_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);

        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
    }

    #[DataProvider('two_missing_keys_provider')]

    public function test_it_returns_errors_if_multiple_keys_are_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);

        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
        $command->expectsOutput('Missing the translation with key: nl.test.test_key2');
    }

    #[DataProvider('one_missing_key_provider')]
    public function test_it_fails_if_key_is_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);
        $command->assertExitCode(1);
    }

    #[DataProvider('one_missing_value_provider')]
    public function test_it_fails_if_value_is_empty($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory
        ]);
        $command->assertExitCode(1);
    }

    #[DataProvider('zero_missing_key_provider')]
    public function test_it_is_successful_if_none_keys_are_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_a_single_language()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::multipleLangs . 'one_language'
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_two_languages()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::multipleLangs . 'two_languages'
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_ten_languages()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::multipleLangs . 'ten_languages'
        ]);

        $command->assertExitCode(0);
    }

    #[DataProvider('zero_missing_key_provider')]
    public function test_it_returns_an_all_good_message_if_everything_is_good($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    #[DataProvider('zero_missing_key_provider')]
    public function test_we_can_exclude_an_directory($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
            '--excludedDirectories' => 'nl',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    #[DataProvider('zero_missing_key_provider')]
    public function test_we_can_exclude_two_directories($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
            '--excludedDirectories' => 'nl,en',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_one_toplevel_language_file()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::jsonDir . 'toplevel_json_files/one',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_two_toplevel_language_file()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::jsonDir . 'toplevel_json_files/two',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_missing_key_in_toplevel_language_file()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::jsonDir . 'toplevel_json_files/missing_key_in_one_lang',
        ]);

        $command->expectsOutput('Missing the translation with key: nl.test_key');

        $command->assertExitCode(1);
    }

    public function test_it_handles_slashes_in_json_keys()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::jsonDir . 'toplevel_json_files/slashes_in_title',
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_two_levels_down()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => self::multipleDirectoriesDir.'two_level_down',
        ]);

        $command->expectsOutput('Missing the translation with key: en.test.test_key');

        $command->assertExitCode(1);
    }

    public static function one_missing_file_provider(): array
    {
        return [
            [self::basicDir . 'one_missing_file'],
            [self::jsonDir . 'one_missing_file'],
        ];
    }

    public static function one_missing_key_provider(): array
    {
        return [
            [self::basicDir . 'one_missing_key'],
            [self::jsonDir . 'one_missing_key'],
        ];
    }

    public static function two_missing_keys_provider(): array
    {
        return [
            [self::basicDir . 'two_missing_keys'],
            [self::jsonDir . 'two_missing_keys'],
        ];
    }

    public static function zero_missing_key_provider(): array
    {
        return [
            [self::basicDir . 'zero_missing_keys'],
            [self::jsonDir . 'zero_missing_keys'],
        ];
    }

    public static function one_missing_value_provider(): array
    {
        return [
            [self::basicDir . 'one_missing_value'],
            [self::jsonDir . 'one_missing_value'],
        ];
    }
}
