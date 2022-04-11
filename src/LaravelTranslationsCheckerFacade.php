<?php

namespace Larswiegers\LaravelTranslationsChecker;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Larswiegers\LaravelTranslationsChecker\Skeleton\SkeletonClass
 */
class LaravelTranslationsCheckerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-translations-checker';
    }
}
