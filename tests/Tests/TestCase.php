<?php

declare(strict_types=1);

namespace Tests;

use Larswiegers\LaravelTranslationsChecker\LaravelTranslationsCheckerServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelTranslationsCheckerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
