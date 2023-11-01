# Not defined but used in blade
The command `translations:generate` is the combination of checking if translations are defined and if not generating them.
This way you can sleep safely at night.

## Command
Use the command below, it is that easy!

```php
php artisan translations:generate
```

## How it works
### Step 1
Find all undefined translations in your blade files and 
missing translations in other languages than your main language. 

### Step 2
Generate the missing translations using an translation service.

## Step 3
Add the translations to your language files.

## Step 4
Manual checking done by you. 
