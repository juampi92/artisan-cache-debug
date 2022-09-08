# 🔎 `php artisan cache:debug`
## Simple artisan command to debug your redis cache

<p align="center">
  <img src="/art/preview.png" width="882" alt="">
  <p align="center">
    <a href="https://packagist.org/packages/juampi92/artisan-cache-debug"><img src="https://img.shields.io/packagist/v/juampi92/artisan-cache-debug.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://github.com/juampi92/artisan-cache-debug/actions?query=workflow%3Arun-tests+branch%3Amain"><img src="https://img.shields.io/github/workflow/status/juampi92/artisan-cache-debug/run-tests?label=tests" alt="GitHub Tests Action Status"></a>
    <a href="https://github.com/juampi92/artisan-cache-debug/actions?query=workflow%3A'Fix+PHP+code+style+issues'+branch%3Amain"><img src="https://img.shields.io/github/workflow/status/juampi92/artisan-cache-debug/Fix%20PHP%20code%20style%20issues?label=code%20style" alt="GitHub Code Style Action Status"></a>
    <a href="https://packagist.org/packages/juampi92/artisan-cache-debug"><img src="https://img.shields.io/packagist/dt/juampi92/artisan-cache-debug.svg?style=flat-square" alt="Total Downloads"></a>
  </p>
</p>

## 🚀 Installation

You can install the package via composer:

```bash
composer require juampi92/artisan-cache-debug --dev
```

> If you would like to debug the cache in production, you can install it without the `--dev` flag.

## Usage

The simplest usage:
```php
php artisan cache:debug
```

### Options

| Option / Flag                   | Description                                                                                                                            |
|---------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| `--key=*`                       | Will filter the keys. Can use wildcard. Example: `--key=*:translations`. [Read more](https://redis.io/commands/scan/#the-match-option) |
| `--heavier-than[=HEAVIER-THAN]` | Will hide keys lighter than X. Use a format like `10bytes`, `1kb`, `8b`                                                                |
| `--sort-by[=SORT-BY]`           | Will sort the keys by `size` or `key`. *[default: "size"]*                                                                             |
| `--sort-dir[=SORT-DIR]`         | Set the sorting direction: `asc` or `desc`.                                                                                            |
| `--forever`                     | Will **only** show non-expiring keys.                                                                                                  |
| `--with-details`                | Show the type of every cache record.                                                                                                   |

## Testing

```bash
sail up -d
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### To-do

Some ideas to expand this package:

- [ ] Pagination
- [ ] Filter on type
- [ ] Display the TTL (currently can't make it fit in the results)
- [ ] Support memcache?

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Juan Pablo Barreto](https://github.com/juampi92)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
