# Quick start

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
[![Total Downloads](https://img.shields.io/packagist/dt/larswiegers/laravel-translations-checker.svg?style=flat-square)](https://packagist.org/packages/larswiegers/laravel-translations-checker)
![GitHub Actions](https://github.com/larswiegers/laravel-translations-checker/actions/workflows/main.yml/badge.svg)

![Laravel translation checker](https://banners.beyondco.de/Laravel%20translation%20checker.png?theme=light&packageManager=composer+require&packageName=larswiegers%2Flaravel-translations-checker&pattern=architect&style=style_1&description=Use+the+laravel+translation+checker+and+get+direct+feedback+where+and+what+translations+you+are+missing%21&md=1&showWatermark=0&fontSize=100px&images=globe)

Ever feel that you are missing translations in some languages you support? Get users emailing you about weird texts on their screen?

Use the **laravel translation checker** and get direct feedback where and what translations you are missing!
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

### What does the output look like?
```
The language nl (resources/lang/nl) is missing the file ( passwords.php )
Missing the translation with key: nl.passwords.reset
Missing the translation with key: nl.passwords.sent
Missing the translation with key: nl.passwords.throttled
Missing the translation with key: nl.passwords.token
Missing the translation with key: nl.passwords.user
```

### Recommendations
We recommend setting up a GitHub action (or any other CI) to run the command on every push to your repository. 
This way you can be sure that you will never forget to check your translations again.
Find out how to do that here: [running in ci](./running-in-ci.md).
