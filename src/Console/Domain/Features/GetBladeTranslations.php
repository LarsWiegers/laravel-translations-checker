<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Larswiegers\LaravelTranslationsChecker\Console\Domain\BladeParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Stillat\BladeParser\Nodes\EchoNode;

class GetBladeTranslations
{
    public string $topDirectoryOrFile;
    /**
     * @var array<string>
     */
    private array $translationsFound = [];

    public function __construct(string $topDirectoryOrFile)
    {
        $this->topDirectoryOrFile = $topDirectoryOrFile;
    }

    /**
     * @return string[]
     */
    public function get(): array
    {
        $path = $this->topDirectoryOrFile;
        if (count($this->translationsFound) > 0) {
            return $this->translationsFound;
        }

        if (FileFacade::isFile($path)) {
            $this->handleFile($path);

            return $this->translationsFound;
        }

        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $bladeFile => $info) {
            if (! Str::endsWith($bladeFile, '.blade.php')) {
                continue;
            }

            $this->handleFile($bladeFile);
        }

        return $this->translationsFound;
    }

    private function handleFile(string $bladeFile): void
    {
        // check if file is blade
        $parser = new BladeParser($bladeFile);
        foreach ($parser->parse() as $node) {
            if ($node instanceof EchoNode) {
                $echoNodeContent = $node->innerContent;

                // Move all text to a single line
                $echoNodeContent = preg_replace("/[\r\n]*/","",$echoNodeContent);

                if (Str::contains($node->innerContent, '__(')) {
                    $echoNodeContent = str_replace('__(\'', '', $echoNodeContent);
                    $echoNodeContent = str_replace('__("', '', $echoNodeContent);

                    $echoNodeContent = str_replace('\')', '', $echoNodeContent);
                    $echoNodeContent = str_replace('")', '', $echoNodeContent);
                }

                if (Str::contains($node->innerContent, 'trans_choice(')) {
                    $echoNodeContent = str_replace('trans_choice(\'', '', $echoNodeContent);
                    $echoNodeContent = str_replace('trans_choice("', '', $echoNodeContent);

                    if (str_contains($echoNodeContent, "'")) {
                        $echoNodeContent = substr($echoNodeContent, 0, strpos($echoNodeContent, "'"));
                    }
                    if (str_contains($echoNodeContent, '"')) {
                        $echoNodeContent = substr($echoNodeContent, 0, strpos($echoNodeContent, '"'));
                    }
                }

                $echoNodeContent = str_replace('\'', '', $echoNodeContent);
                $echoNodeContent = str_replace('"', '', $echoNodeContent);
                $echoNodeContent = str_replace('. .', '.', $echoNodeContent);


                $this->translationsFound[] = $echoNodeContent;
            }

        }
    }
}
