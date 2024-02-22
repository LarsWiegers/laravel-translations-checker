# Not defined but used in blade
The command `translations:blade` is a way to check if translations used in blade are defined in your languages.
This way you can freely add translations to your blade files without having to worry about forgetting to define them.
This saves time during development and makes sure you don't have to go back and forth to define translations.

## Command
Use the command below, it is that easy!

```php
php artisan translations:blade
```

### What does the output look like?
Lets say you have a blade file with the following line:
```php
{{ __('welcome.heading') }}
```
and the following translation file (welcome.php):
```php
return [
    'paragraph' => 'Test paragraph!'
    // See that the heading key is not here
];
```
The output will look like this:
```
'The translation: "welcome.heading" is used in blade but not defined in the language files.');
```

## Why is it important
Adding translations during development takes you away from your flow. Which we all know is very important. That is why
we recommend running this command before pushing or in CI that way you only have to define the translations once.

## Exit code
The command will exit with code 1 if there are translations used in blade that are not defined in the language files.
This way you can use it in your CI to make sure you don't push code with undefined translations.
