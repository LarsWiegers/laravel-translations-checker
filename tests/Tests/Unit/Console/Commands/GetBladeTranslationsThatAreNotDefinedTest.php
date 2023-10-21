<?php

namespace Console\Commands;

use Tests\TestCase;

class GetBladeTranslationsThatAreNotDefinedTest extends TestCase
{
    private string $topDirectory = 'tests/resources/blade/command';

    public function test_it_can_find_basic_blade_translations_that_are_not_defined()
    {
        $command = $this->artisan('translations:blade', [
            '--topDirectory' => $this->topDirectory.'/basic',
        ]);

        $command->assertOk();
    }

    public function test_it_finds_the_used_but_not_defined_translation()
    {
        $command = $this->artisan('translations:blade', [
            '--topDirectory' => $this->topDirectory.'/one',
        ]);
        $command->expectsOutput('The translation: "welcome.paragraph_two" is used in blade but not defined in the language files.');

        $command->assertFailed();
    }
}
