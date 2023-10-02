<?php

namespace Console\Commands;

use Tests\TestCase;

class CheckExcludeKeysTest extends TestCase
{
    private string $exclusionDir = 'tests/resources/lang/excluded_keys';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItWorksFineIfKeyExistButIsExcluded()
    {
        $command = $this->artisan('translations:check', [
            '--directory' => $this->exclusionDir .'/excluded_but_existing',
            '--excludedKeys' => 'test.existing_key',
        ]);

        $command->assertExitCode(0);
    }

    public function testItWorksFineIfKeyIsMissingButIsExcluded()
    {
        config()->set('translations-checker.excluded_keys', null);


        $command = $this->artisan('translations:check', [
            '--directory' => $this->exclusionDir .'/excluded_but_missing',
            '--excludedKeys' => 'existing_key',
        ]);

        $command->assertExitCode(0);
    }
}
