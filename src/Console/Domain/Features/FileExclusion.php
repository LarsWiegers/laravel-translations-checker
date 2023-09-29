<?php

namespace Larswiegers\LaravelTranslationsChecker\Console\Domain\Features;

class FileExclusion
{
    const EXCLUDE_MAC_FILES = ['.DS_Store'];

    public static function shouldExclude($language): bool
    {
        return in_array($language, self::EXCLUDE_MAC_FILES);
    }
}
