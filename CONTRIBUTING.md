# Contributing
1. Clone repository into a `ps_arengu_auth` directory inside the `modules` directory of an existing PrestaShop installation and `cd` into it.
2. Run `composer install --dev`.

## Coding standards
We follow PrestaShop's own [coding standards](https://devdocs.prestashop.com/1.7/development/coding-standards/). `php-cs-fixer` can help checking and fixing code by running `vendor/bin/php-cs-fixer fix . --config=../../.php_cs.dist` directly from the repository directory.

