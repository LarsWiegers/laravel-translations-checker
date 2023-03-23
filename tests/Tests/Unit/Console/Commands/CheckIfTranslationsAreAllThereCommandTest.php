<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CheckIfTranslationsAreAllThereCommandTest extends TestCase
{

    public $basicDir = "tests/resources/lang/basic/";
    public $jsonDir = "tests/resources/lang/json/";
    public $multipleLangs  = "tests/resources/lang/multi_langs/";

    /**
     * @dataProvider one_missing_key_provider
     *
     * @return void
     */
    public function test_it_returns_errors_if_one_key_is_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
        ]);

        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
    }

    /**
     * @dataProvider two_missing_keys_provider
     *
     * @return void
     */
    public function test_it_returns_errors_if_multiple_keys_are_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' =>  $directory
        ]);
        
        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
        $command->expectsOutput('Missing the translation with key: nl.test.test_key2');
    }

    /**
     * @dataProvider one_missing_key_provider
     *
     * @return void
     */
    public function test_it_fails_if_key_is_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory
        ]);
        $command->assertExitCode(1);
    }

    /**
     * @dataProvider zero_missing_key_provider
     *
     * @return void
     */
    public function test_it_is_successful_if_none_keys_are_missing($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_a_single_language()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->multipleLangs . 'one_language'
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_two_languages()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->multipleLangs . 'two_languages'
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_handles_ten_languages()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->multipleLangs . 'ten_languages'
        ]);

        $command->assertExitCode(0);
    }
    /**
     * @dataProvider zero_missing_key_provider
     *
     * @return void
     */
    public function test_it_returns_an_all_good_message_if_everything_is_good($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    /**
     * @dataProvider zero_missing_key_provider
     *
     * @return void
     */
    public function test_we_can_exclude_an_directory($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
            '--excludedDirectories' => 'nl'
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    /**
     * @dataProvider zero_missing_key_provider
     *
     * @return void
     */
    public function test_we_can_exclude_two_directories($directory)
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $directory,
            '--excludedDirectories' => 'nl,en'
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_one_toplevel_language_file() {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->jsonDir . 'toplevel_json_files/one',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_two_toplevel_language_file() {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->jsonDir . 'toplevel_json_files/two',
        ]);

        $command->expectsOutput('✔ All translations are okay!');

        $command->assertExitCode(0);
    }

    public function test_it_handles_missing_key_in_toplevel_language_file() {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->jsonDir . 'toplevel_json_files/missing_key_in_one_lang',
        ]);

        $command->expectsOutput('Missing the translation with key: nl.test_key');

        $command->assertExitCode(1);
    }


    public function one_missing_file_provider(): array
    {
        return [
            [$this->basicDir . 'one_missing_file'],
            [$this->jsonDir . 'one_missing_file'],
        ];
    }

    public function one_missing_key_provider(): array
    {
        return [
            [$this->basicDir . 'one_missing_key'],
            [$this->jsonDir . 'one_missing_key'],
        ];
    }

    public function two_missing_keys_provider(): array
    {
        return [
            [$this->basicDir . 'two_missing_keys'],
            [$this->jsonDir . 'two_missing_keys'],
        ];
    }

    public function zero_missing_key_provider(): array
    {
        return [
            [$this->basicDir . 'zero_missing_keys'],
            [$this->jsonDir . 'zero_missing_keys'],
        ];
    }
}
