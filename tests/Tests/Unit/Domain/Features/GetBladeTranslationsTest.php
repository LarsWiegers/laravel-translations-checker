<?php

namespace Larswiegers\LaravelTranslationsChecker\Tests\Tests\Unit\Console\Commands;

use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetBladeTranslations;
use Tests\TestCase;

class GetBladeTranslationsTest extends TestCase
{
    private string $basicDir = 'tests/resources/blade/basic/';

    private string $complexDir = 'tests/resources/blade/complex/';

    /** BASIC */
    public function testItCanGetABasicTranslation()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'single');
        $this->assertContains('messages.welcome', $feature->get());
    }
    public function testItCanGetMultipleTranslations()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multiple');
        $this->assertContains('messages.welcome', $feature->get());
        $this->assertContains('messages.welcome2', $feature->get());
    }
    public function testItCanHandleDoubleQuotes()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'double-quotes');
        $this->assertContains('messages.welcome', $feature->get());
    }
    public function testItCanHandleTransChoiceSingle()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'trans-choice/basic.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }
    public function testItCanHandleTransChoiceDouble()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'trans-choice/single.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }
    public function testItHandlesTransChoiceWithReplaces()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'trans-choice/with-replaces.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }
    public function testItHandlesMultiLine()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/basic-single.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }
    public function testItHandlesALotOfLinesInMulti()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/a-lot-of-lines.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }
    public function testDoubleQuotesMultiLine()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/basic-single.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }

    /** COMPLEX */
    public function testItHandlesALotOfHtml()
    {
        $feature = new GetBladeTranslations($this->complexDir . 'a-lot-of-html/index.blade.php');

        $this->assertContains('messages.header', $feature->get());
        $this->assertContains('messages.welcome', $feature->get());
        $this->assertContains('messages.about', $feature->get());
        $this->assertContains('messages.list', $feature->get());
        $this->assertContains('messages.contact', $feature->get());
        $this->assertContains('messages.welcome.text', $feature->get());
        $this->assertContains('messages.footer', $feature->get());
        $this->assertCount(7, $feature->get());
    }
    public function testItCanRunMultipleTimesAndGetTheSameResult()
    {
        $feature = new GetBladeTranslations($this->complexDir . 'a-lot-of-html/index.blade.php');
        $this->assertCount(7, $feature->get());
        $this->assertCount(7, $feature->get());
        $this->assertCount(7, $feature->get());
    }
    public function testItHandlesSubDirectories()
    {
        $feature = new GetBladeTranslations($this->complexDir . 'a-lot-of-sub-directories');
        $this->assertCount(7, $feature->get());
        $this->assertContains('messages.header', $feature->get());
        $this->assertContains('messages.welcome', $feature->get());
        $this->assertContains('messages.about', $feature->get());
        $this->assertContains('messages.list', $feature->get());
        $this->assertContains('messages.contact', $feature->get());
        $this->assertContains('messages.welcome.text', $feature->get());
        $this->assertContains('messages.footer', $feature->get());
    }

}
