# Laravel translations checker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
[![Total Downloads](https://img.shields.io/packagist/dt/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
![GitHub Actions](https://github.com/larswiegers/laravel-translations-checker/actions/workflows/main.yml/badge.svg)

![Laravel translation checker](https://banners.beyondco.de/Laravel%20translation%20checker.png?theme=light&packageManager=composer+require&packageName=larswiegers%2Flaravel-translations-checker&pattern=architect&style=style_1&description=Use+the+laravel+translation+checker+and+get+direct+feedback+where+and+what+translations+you+are+missing%21&md=1&showWatermark=0&fontSize=100px&images=globe)

Ever feel that you are missing translations in some languages you support? Get users emailing you about weird strings on their screen?

Use the laravel translation checker and get direct feedback where and what translations you are missing!
## Installation

You can install the package via composer:

```bash
composer require larswiegers/laravel-translations-checker
```

## Usage
Use the command below, it is that easy!
```php
php artisan translations:check
```
### Different directory
Are your translations in a weird directory? use the --directory option like this:
```php
php artisan translations:check --directory=resources/lang
```
### Exclude directories
Some packages have their own language files, it is probably smart to exclude them. 
```php
php artisan translations:check --excludedDirectories=lang/vendor
```

This option is also available as configuration option 'excluded_directories'.

For example:
```php
    'excluded_directories' => ['lang/vendor'],
```

### Exclude languages
This section provides instructions on how to exclude specific languages from being checked.

To exclude languages, follow these steps:

1. Open the project's configuration file.

2. Locate the `translation-checker` file.

3. Add the language codes of the languages you want to exclude to the `exclude_languages` field.

For example:
```
    exclude_languages = ["en", "fr", "es"]
```

### JSON support
The package supports both .php files and .json files for the translations.

### Running in github actions?
```
  translations:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
          extensions: mbstring, intl
          ini-values: post_max_size=256M, max_execution_time=180
          coverage: xdebug
          tools: php-cs-fixer, phpunit
      - name: Install Dependencies
        run: composer install -q --no-interaction --no-scripts
      - name: Run translations check
        run: php artisan translations:check --excludedDirectories=vendor
```

### What does the output look like?
```
The language nl (resources/lang/nl) is missing the file ( passwords.php )
Missing the translation with key: nl.passwords.reset
Missing the translation with key: nl.passwords.sent
Missing the translation with key: nl.passwords.throttled
Missing the translation with key: nl.passwords.token
Missing the translation with key: nl.passwords.user
```
### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email larswiegers@live.nl instead of using the issue tracker.

## Credits

-   [Lars Wiegers](https://github.com/larswiegers)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
