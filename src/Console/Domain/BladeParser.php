<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain;

use Stillat\BladeParser\Nodes\AbstractNode;
use Stillat\BladeParser\Parser\DocumentParser;

class BladeParser
{
    public function __construct(public string $fileName)
    {
    }

    /**
     * @return AbstractNode[]
     */
    public function parse(): array
    {
        $file = \Illuminate\Support\Facades\File::get($this->fileName);
        $parser = new DocumentParser;

        return $parser->parse($file);
    }
}
