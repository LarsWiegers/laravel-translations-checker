<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exclude Languages
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to exclude specific languages from
    | being processed or checked. The array contains the language codes that
    | should be excluded from the language lists.
    |
    | Example: ['en-ca', 'en-us']
    |
    */
    'exclude_languages' => [],

    /*
    |--------------------------------------------------------------------------
    | Excludes Directories
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to exclude specific directories from
    | being processed or checked. The array contains the directory path that
    | should be excluded from being scanned. It can be overridden by passing the
    | command line option "--excludedDirectories=...". Pass command line option
    | "--excludedDirectories=none" to not exclude any directory.
    |
    | Example: ['lang/vendor']
    |
    */
    'excluded_directories' => [],
];
