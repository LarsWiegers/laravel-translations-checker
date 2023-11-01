# Running in CI
We recommend setting up a GitHub action (or any other CI) to run the command on every push to your repository.

Copy and paste (GitHub actions): 
```yaml
name: translations-checker
on:
    push:
        branches: [main]
    pull_request:
        branches: [main]
jobs:
  translations:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mbstring, intl
          ini-values: post_max_size=256M, max_execution_time=180
          coverage: xdebug
          tools: php-cs-fixer, phpunit
      - name: Install Dependencies
        run: composer install -q --no-interaction --no-scripts
      - name: Run translations check
        run: php artisan translations:check --excludedDirectories=vendor
