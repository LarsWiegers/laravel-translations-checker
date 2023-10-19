<?php

namespace Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GetBladeTranslationsThatAreNotDefinedTest extends TestCase
{
    private string $languagesDir = 'tests/resources/blade/command/basic';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test excluded languages.
     *
     * @return void
     */
    public function testItGetsTheRealLines()
    {
        $command = $this->withoutMockingConsoleOutput()->artisan('translations:blade', [
            '--langDirectory' => $this->languagesDir . '/lang',
            '--bladeDirectory' => $this->languagesDir . '/resources',
        ]);
        dd(Artisan::output());

        $command->assertExitCode(0);
    }
}
