<?php

namespace Console\Commands;

use Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    public function test_it_can_generate_a_translated_version_of_1_file()
    {
        $generatedFileName = 'test_it_can_generate_a_translated_version_of_1_file.php';
        $command = $this->withoutMockingConsoleOutput()->artisan('translations:generate', [
            '--inputFile' => 'tests/resources/lang/basic/one_missing_file/en/test.php',
            '--sourceLanguage' => 'en',
            '--targetLanguage' => 'nl',
            '--outputFile' => 'tests/resources/generated/' . $generatedFileName,
        ]);
        dd($command);
        $this->assertTrue(true);
    }
}
