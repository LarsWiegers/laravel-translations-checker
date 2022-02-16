<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

final class CommandCanHandleErrorsTest extends TestCase
{

    public $basicDir = "tests/resources/lang/basic/";

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
}
