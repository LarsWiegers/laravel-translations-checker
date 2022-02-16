# Laravel translations checker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
[![Total Downloads](https://img.shields.io/packagist/dt/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
![GitHub Actions](https://github.com/larswiegers/laravel-translations-checker/actions/workflows/main.yml/badge.svg)

Ever feel that you are missing translations in some languages you support? Get users emailing you about weird strings on their screen?

Use the laravel translation checker and get direct feedback where and what translations you are missing!
## Installation

You can install the package via composer:

```bash
composer require larswiegers/laravel-translations-checker
```

## Usage

```php
php artisan translations:check
```

Are your translations in a weird directory? use the --directory option like this:

```php
php artisan translations:check --directory=resources/lang
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
