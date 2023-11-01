# Not defined but used in blade
The command `translations:blade` is a way to check if translations used in blade are defined in your languages.
This way you can freely add translations to your blade files without having to worry about forgetting to define them.
This saves time during development and makes sure you don't have to go back and forth to define translations.

## Command
Use the command below, it is that easy!

```php
php artisan translations:check
```

### What does the output look like?
```

```

## Why is it important
Adding translations during development takes you away from your flow. Which we all know is very important. That is why
we recommend running this command before pushing or in CI that way you only have to define the translations once.
