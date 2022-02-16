<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

final class CheckIfTranslationsAreAllThereCommandTest extends TestCase
{

    public $basicDir = "tests/resources/lang/basic/";
    public $multipleLangs  = "tests/resources/lang/multi_langs/";

    public function test_it_returns_errors_if_one_key_is_missing()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'one_missing_key'
        ]);
        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
    }

    public function test_it_returns_errors_if_multiple_keys_are_missing()
    {
        $command = $this->artisan('translations:check', [
            '--directory' =>  $this->basicDir . 'two_missing_keys'
        ]);
        
        $command->expectsOutput('Missing the translation with key: nl.test.test_key');
        $command->expectsOutput('Missing the translation with key: nl.test.test_key2');
    }

    public function test_it_fails_if_key_is_missing()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'one_missing_key'
        ]);
        $command->assertExitCode(1);
    }

    public function test_it_is_successful_if_none_keys_are_missing()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'zero_missing_keys'
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
}
