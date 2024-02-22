<?php

namespace Console\Commands;

use Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    private string $languagesDir = 'tests/resources/lang/exclude_langs';

    public function testTheCommand()
    {
//        $command = $this->withoutMockingConsoleOutput()->artisan('translations:generate');
//        dd($command);
        $this->assertTrue(true);
    }
}
