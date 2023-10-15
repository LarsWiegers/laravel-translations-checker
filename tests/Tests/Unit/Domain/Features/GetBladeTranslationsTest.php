<?php

namespace Larswiegers\LaravelTranslationsChecker\Tests\Tests\Unit\Console\Commands;

use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetBladeTranslations;
use Tests\TestCase;

class GetBladeTranslationsTest extends TestCase
{
    private string $basicDir = 'tests/resources/blade/basic/';

    public function testItCanGetABasicTranslation()
    {
        $feature = new GetBladeTranslations($this->basicDir.'single');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function testItCanGetMultipleTranslations()
    {
        $feature = new GetBladeTranslations($this->basicDir.'multiple');
        $this->assertContains('messages.welcome', $feature->get());
        $this->assertContains('messages.welcome2', $feature->get());
    }

    public function testItCanHandleDoubleQuotes()
    {
        $feature = new GetBladeTranslations($this->basicDir.'double-quotes');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function testItCanHandleTransChoiceSingle()
    {
        $feature = new GetBladeTranslations($this->basicDir.'trans-choice/basic.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function testItCanHandleTransChoiceDouble()
    {
        $feature = new GetBladeTranslations($this->basicDir.'trans-choice/double-quotes.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function testItHandlesTransChoiceWithReplaces()
    {
        $feature = new GetBladeTranslations($this->basicDir.'trans-choice/with-replaces.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }
}
