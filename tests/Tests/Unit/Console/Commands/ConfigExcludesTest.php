<?php

declare(strict_types=1);

namespace Console\Commands;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class ConfigExcludesTest extends TestCase
{

    public string $excludesDir = "tests/resources/lang/excluded_keys/";
    public function test_it_is_okay_if_we_dont_pass_in_excluded_keys()
    {
        $dir = $this->excludesDir . 'no_excludes';
        $command = $this->artisan('translations:check', [
            '--directory' => $dir,
            '--excludedKeys' => []
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_excludes_a_key_if_it_is_passed_in()
    {
        $dir = $this->excludesDir . 'exclude_one_missing_key';
        $command = $this->artisan('translations:check', [
            '--directory' => $dir,
            '--excludedKeys' => implode(',', [
                'test_key'
            ])
        ]);

        $command->assertExitCode(0);
    }

    public function test_it_can_get_them_from_config()
    {
        $dir = $this->excludesDir . 'exclude_one_missing_key';

        Config::set('translation-checker.excluded_keys', ['test_key']);

        $command = $this->artisan('translations:check', [
            '--directory' => $dir
        ]);

        $command->assertExitCode(0);
    }
}
