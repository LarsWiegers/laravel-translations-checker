<?php

namespace Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GetBladeTranslationsThatAreNotDefinedTest extends TestCase
{
    private string $topDirectory = 'tests/resources/blade/command/basic';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test excluded languages.
     *
     * @return void
     */
    public function test_it_can_find_basic_blade_translations_that_are_not_defined()
    {
        $command = $this->artisan('translations:blade', [
            '--topDirectory' => $this->topDirectory,
            '--langDirectory' => $this->topDirectory . '/lang',
            '--bladeDirectory' => $this->topDirectory . '/resources',
        ]);

        $command->assertExitCode(0);
    }
}
