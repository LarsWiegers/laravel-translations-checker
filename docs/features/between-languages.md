# Checking between languages
The command `translations:check` is a great way to check if you have all translations in all languages.

## Command
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

## Why is it important
It is easy to forget to add a translation in all languages, or maybe you are waiting on a translator to translate a certain key.
This command will help you find those missing translations.

## Exit code
The command will exit with code 1 if there are translations defined in 1 language but not in others.
This way you can use it in your CI to make sure you don't push code with not defined translations.
