<?php

namespace Console\Commands;

use Tests\TestCase;

class CheckExcludeMacFilesTest extends TestCase
{
    private string $languagesDir = 'tests/resources/lang/exclude_mac_files';
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItSkipsTotallyFineIfDSStoreExists()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->languagesDir . '/ds-store'
        ]);

        $command->assertExitCode(0);
    }
}
