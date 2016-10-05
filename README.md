# URL Expander

[![Build Status](https://img.shields.io/travis/pragmatist/url-expander/master.svg?style=flat-square)](https://travis-ci.org/pragmatist/url-expander)
[![Quality Score](https://img.shields.io/scrutinizer/g/pragmatist/url-expander.svg?style=flat-square)](https://scrutinizer-ci.com/g/pragmatist/url-expander)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Version](https://img.shields.io/github/release/pragmatist/url-expander.svg?style=flat-square)](https://github.com/pragmatist/url-expander/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/pragmatist/url-expander.svg?style=flat-square)](https://packagist.org/packages/pragmatist/url-expander)

The `UrlExpander` package provides a simple library to expand short URLs.

## System Requirements

You need **PHP >= 7.0** to use this library.

## Installation

Install `UrlExpander` using Composer.

```bash
$ composer require pragmatist/url-expander
```

## Usage

Simple redirect-based URL expansion:

```php
<?php

use Pragmatist\UrlExpander\RedirectBasedUrlExpander;
use League\Uri\Schemes\Http;

$expander = RedirectBasedUrlExpander::createWithGuzzleClient();
$expandedUri = $expander->expand(
    Http::createFromString('http://bit.ly/2dtGsBS')
);

echo $expandedUri; // Outputs: https://github.com/pragmatist/url-expander
```

Use the CachingUrlExpander to cache expanded URLs in a PSR-6 compatible cache:

```php
<?php

use Cache\Adapter\PHPArray\ArrayCachePool;
use Pragmatist\UrlExpander\CachingUrlExpander;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander;
use League\Uri\Schemes\Http;

$expander = new CachingUrlExpander(
    RedirectBasedUrlExpander::createWithGuzzleClient(),
    new ArrayCachePool(); // From the cache/array-adapter package
);

$shortUri = Http::createFromString('http://bit.ly/2dtGsBS');
echo $expander->expand($shortUri); // Outputs: https://github.com/pragmatist/url-expander
echo $expander->expand($shortUri); // From cache
```

## Testing

`UrlExpander` has a [PHPUnit](https://phpunit.de/) test suite. To run the tests, run the following command from the project folder.

```bash
$ ./vendor/bin/phpunit
```

## Security

If you discover any security related issues, please email hello@pragmatist.nl instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
