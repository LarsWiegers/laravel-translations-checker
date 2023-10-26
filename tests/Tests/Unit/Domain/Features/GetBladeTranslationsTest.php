<?php

namespace Larswiegers\LaravelTranslationsChecker\Tests\Tests\Unit\Console\Commands;

use Larswiegers\LaravelTranslationsChecker\Console\Domain\Features\GetBladeTranslations;
use Tests\TestCase;

class GetBladeTranslationsTest extends TestCase
{
    private string $basicDir = 'tests/resources/blade/basic/';

    private string $complexDir = 'tests/resources/blade/complex/';

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
        $feature = new GetBladeTranslations($this->basicDir . 'trans-choice/double-quotes.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function testItHandlesTransChoiceWithReplaces()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'trans-choice/with-replaces.blade.php');
        $this->assertContains('messages.welcome', $feature->get());
    }

    public function test_it_handles_a_lot_of_html()
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

    public function test_it_can_run_multiple_times_and_get_the_same_result()
    {
        $feature = new GetBladeTranslations($this->complexDir . 'a-lot-of-html/index.blade.php');
        $this->assertCount(7, $feature->get());
        $this->assertCount(7, $feature->get());
        $this->assertCount(7, $feature->get());
    }

    public function test_it_handles_sub_directories()
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

    public function test_it_handles_multi_line()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/basic-single.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }

    public function test_it_handles_a_lot_of_lines_in_multi()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/a-lot-of-lines.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }

    public function test_double_quotes_multi_line()
    {
        $feature = new GetBladeTranslations($this->basicDir . 'multi-line/double-quotes.blade.php');
        $this->assertContains('messages.hello', $feature->get());
        $this->assertCount(1, $feature->get());
    }
}
