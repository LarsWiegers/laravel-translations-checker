<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class CommandCanHandleErrorsTest extends TestCase
{

    public $basicDir = "tests/resources/lang/basic/";
    public $jsonDir = "tests/resources/lang/json/";

    public function test_it_gives_a_warning_if_the_passed_dir_is_missing()
    {
        $dir = $this->basicDir . 'does_not_exist';
        $command = $this->artisan('translations:check', [
            '--directory' => $dir
        ]);

        $command->expectsOutput('The passed directory (' . $dir . ') does not exist.');
    }

    public function test_it_fails_if_the_passed_dir_is_missing()
    {
        $dir = $this->basicDir . 'does_not_exist';
        $command = $this->artisan('translations:check', [
            '--directory' => $dir
        ]);

        $command->assertExitCode(1);
    }

    public function test_it_fails_with_a_missing_file()
    {
        $dir = $this->basicDir . 'one_missing_file';
        $command = $this->artisan('translations:check', [
            '--directory' => $dir
        ]);

        $command->expectsOutput('The language ' . 'nl' . ' (' . $dir . '/nl' . ') is missing the file ( ' . 'test.php' . ' )');

        $command->assertExitCode(1);
    }


    public function test_we_handle_if_the_dir_does_not_exist()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'zero_missing_keys',
            '--excludedDirectories' => 'this_dir_does_not_exist'
        ]);

        $command->assertExitCode(0);
    }

    public function test_we_handle_empty_excluded_directories()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'zero_missing_keys',
            '--excludedDirectories' => ''
        ]);

        $command->assertExitCode(0);
    }

    public function test_we_can_go_in_sub_directories()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->basicDir . 'zero_missing_keys',
            '--excludedDirectories' => ''
        ]);

        $command->assertExitCode(0);
    }
}
