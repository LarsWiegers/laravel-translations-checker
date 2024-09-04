<?php

namespace Console\Commands;

use Tests\TestCase;

class CheckExcludeExtensionsTest extends TestCase
{
    private string $directory = 'tests/resources/lang/json/one_missing_file';
    public function testItSkipsDefinedExtensions(): void
    {
        $command = $this->artisan('translations:check', [
            '--excludedFileExtensions' => 'json',
            '--directory' => $this->directory
        ]);

        $command->assertExitCode(0);
    }
}
